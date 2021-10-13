<?php

namespace Drupal\commerce_mpesa\PluginForm\OffsiteRedirect;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class MpesaOffsiteForm extends BasePaymentOffsiteForm {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;
    $gatewayId=$payment->getPaymentGatewayId();

    $order=$payment->getOrder();
    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();
    $configuration = $payment_gateway_plugin->getConfiguration();

    $redirect_url =Url::fromRoute('commerce_mpesa.redirect_stk')->toString();

    $data['mpesaAPI_shortcode'] = $configuration['mpesaAPI_shortcode'];
    $data['mpesaAPI_passkey'] = $configuration['mpesaAPI_passkey'];
    $data['mpesaAPI_callback_url'] = $configuration['mpesaAPI_callback_url'];
    $data['mpesaAPI_name'] = $configuration['mpesaAPI_name'];
    $data['mode'] = $configuration['mode'];
    $data['mpesaAPI_pass'] = $configuration['mpesaAPI_pass'];
    $data['gatewayID'] = $gatewayId;
    $data['order_id']=$payment->getOrderId();#order id
    $data['usermail']=$order->getCustomer()->getEmail();
    $data['total'] = $payment->getAmount()->getNumber();
    // Order and billing address.
    /*$billing_address = $order->getBillingProfile()->get('field_user_code');
    $codearray=$billing_address->getValue();
    $v=$billing_address->getFieldDefinition()->getSettings('allowed_values');
    $data['customer_code']=$codearray[0]['value'];*/

    $data['return']=$form['#return_url'];
    $form = $this->buildRedirectForm($form, $form_state,  $redirect_url, $data, self::REDIRECT_POST);

    return $form;
  }

}
