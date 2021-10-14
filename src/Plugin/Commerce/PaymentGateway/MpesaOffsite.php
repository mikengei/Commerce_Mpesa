<?php

namespace Drupal\commerce_mpesa\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the Off-site Redirect payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "mpesa_stk_payment",
 *   label = "Mpesa STK ",
 *   display_label = "Lipa na Mpesa",
 *   forms = {
 *     "offsite-payment" = "Drupal\commerce_mpesa\PluginForm\OffsiteRedirect\MpesaOffsiteForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa",
 *   },
 *   requires_billing_information = FALSE,
 * )
 */

class MpesaOffsite extends OffsitePaymentGatewayBase{

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  //build the ui for the configuration for payment
  public function defaultConfiguration() {
    return [
        'mpesaAPI_shortcode' => '',
        'mpesaAPI_passkey'=>'',
        'mpesaAPI_callback_url' => '',
        'mpesaAPI_name'=>'',
        'mpesaAPI_pass'=>''
        ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['mpesaAPI_shortcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('BusinessShortCode'),
      '#default_value' => $this->configuration['mpesaAPI_shortcode'],
      '#required' => TRUE,
      '#description' => t('Your Organization shortcode'),
    ];

    $form['mpesaAPI_passkey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('PassKey'),
      '#default_value' => $this->configuration['mpesaAPI_passkey'],
      '#required' => TRUE,
      '#description' => t('Set your API pass-key'),
    ];

    $form['mpesaAPI_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Basic Auth username'),
      '#default_value' => $this->configuration['mpesaAPI_name'],
      '#required' => TRUE,
      '#description' => t('Set your API username string'),
    ];

    $form['mpesaAPI_pass'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Basic Auth password'),
      '#default_value' => $this->configuration['mpesaAPI_pass'],
      '#required' => TRUE,
      '#description' => t('Set your API password string'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $values = $form_state->getValue($form['#parents']);
    $this->configuration['mpesaAPI_shortcode'] = $values['mpesaAPI_shortcode'];
    $this->configuration['mpesaAPI_passkey'] = $values['mpesaAPI_passkey'];
    $this->configuration['mpesaAPI_pass'] = $values['mpesaAPI_pass'];
    $this->configuration['mpesaAPI_name'] = $values['mpesaAPI_name'];
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request) {
    $checkout_id=$request->getQueryString();

    if(!empty($checkout_id)){
      try {
         $conn=\Drupal::service('database');
         $query=$conn->query(
            "SELECT checkoutRequestID
             FROM {commerce_mpesa_ipn}
             WHERE checkoutRequestID= :check_req_id",
             [ ':check_req_id' => $checkout_id ]
         );
         $query->allowRowCount=TRUE;
         if($query->rowCount() === 1 ){
              //save payment
           $order_id =$order->id();
           $orderAmount = round($order->getTotalPrice()->getNumber());
           $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
           //create payment
           $payment = $payment_storage->create([
             'state' => 'Paid',
             'amount' => $order->getTotalPrice(),
             'completed' => 'Mpesa-Success',
             'payment_gateway' => $this->entityId,
             'order_id' => $order_id,
             'remote_id' => $checkout_id,
             'remote_state' => 'Mpesa Received',
           ]);

           $payment->save();
           \Drupal::logger('Commerce Mpesa')->notice('Payment Success on order: @order', array('@order' => $order_id));
           \Drupal::messenger()->addStatus('Payment was successfully');
         }else{
           throw new PaymentGatewayException('There was a Problem processing your payment...');
         }

      }catch (\Exception $e){
        \Drupal::logger('Commerce Mpesa')->error($e->getMessage());
      }

    }else{
      throw new PaymentGatewayException('Payment has not been received');
    }
  }

  /**
   * @inheritDoc
   */
  public function onNotify(Request $request)
  {
    parent::onNotify($request);

    $resp_array=json_decode($request->getContent(), TRUE);
    $num=$resp_array['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
    try {
        $conn = \Drupal::service('database');
        $query = $conn->insert('commerce_mpesa_ipn')->fields(
          [
            'phone_num' => $num,
            'merchantRequestID' => $resp_array['Body']['stkCallback']['MerchantRequestID'],
            'checkoutRequestID' => $resp_array['Body']['stkCallback']['CheckoutRequestID'],
            'mpesa_ref_id' => $resp_array['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'],
            'resp_code' => $resp_array['Body']['stkCallback']['ResultCode'],
            'resp_desc' => $resp_array['Body']['stkCallback']['ResultDesc'],
            'amount' => $resp_array['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'],
            'timestamp' => strtotime($resp_array['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'])
          ]
        )->execute();
          //log entry
         \Drupal::logger('Commerce Mpesa')->info('Response received for: '.$num);
      }catch (\Exception $e){
         //log
        \Drupal::logger('Commerce Mpesa IPN')->error($e);
      }
  }

}
