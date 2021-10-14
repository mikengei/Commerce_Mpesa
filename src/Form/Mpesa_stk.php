<?php


namespace Drupal\commerce_mpesa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Mpesa_stk extends FormBase{

  const MPESA_LIVE_URLS = [
    'token_url' => 'https://api.ravepay.co',
    'stk_url'   => 'https://api.ravepay.co'
  ];

  const MPESA_STAGING_URL = [
    'token_url' => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
    'stk_url'   => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
  ];

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * The total amount for user to pay
   * @var $the_amount
   */
  public $the_amount;

  /**
   * The Mpesa CheckoutID
   * @var $checkoutId
   */
  public $checkoutId;

  public $shortcode;
  public $passkey;
  public $api_pass;
  public $api_name;
  public $api_callback;
  public $orderID;
  public $client, $return_url;
  /**
   * Constructs a new req.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(RequestStack $request_stack) {
    $this->currentRequest = $request_stack->getCurrentRequest();
    $this->client = \Drupal::httpClient();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  /**
     * @inheritDoc
     */
  public function getFormId()
    {
        return 'mpesaform_stk';
    }

  public function get_data(){
       $this->the_amount= $this->currentRequest->request->get('total');
       $this->shortcode= $this->currentRequest->request->get('mpesaAPI_shortcode');
       $this->passkey= $this->currentRequest->request->get('mpesaAPI_passkey');
       $this->api_pass= $this->currentRequest->request->get('mpesaAPI_pass');
       $this->api_name= $this->currentRequest->request->get('mpesaAPI_name');
       $this->orderID=$this->currentRequest->request->get('order_id');
       $this->return_url= $this->currentRequest->request->get('return');
       $this->api_callback=$this->currentRequest->request->get('gatewayID');
  }

  /**
     * @inheritDoc
     */
  public function buildForm(array $form, FormStateInterface $form_state)
    {
      //set configs
      $this->get_data();

      /*$form_state->set('amount',   $this->the_amount);
      $form_state->set('passkey',  $apasskey);
      $form_state->set('api_pass', $this->api_pass);
      $form_state->set('api_name', $this->api_name);
      $form_state->set('return_url',$this->return_url);*/

      $form['shortcode']=[
        '#title'          => t('Shortcode'),
        '#type'           => 'hidden',
        '#maxlength'      => 10,
        '#size'           => 20,
        '#default_value'  => $this->shortcode,
        '#description'    => t('THe shortcode'),
        '#required'       => TRUE
      ];
      $form['amount']=[
        '#title'          => t('Amount'),
        '#type'           => 'hidden',
        '#maxlength'      => 100,
        '#size'           => 100,
        '#default_value'  => $this->the_amount,
        '#description'    => t('THe shortcode'),
        '#required'       => TRUE
      ];
      $form['passkey']=[
        '#title'          => t('Pass key'),
        '#type'           => 'hidden',
        '#maxlength'      => 100,
        '#size'           => 100,
        '#default_value'  =>  $this->passkey,
        '#description'    => t('The passkey'),
        '#required'       => TRUE
      ];
      $form['api_pass']=[
        '#title'          => t('Pass '),
        '#type'           => 'hidden',
        '#maxlength'      => 100,
        '#size'           => 100,
        '#default_value'  =>  $this->api_pass,
        '#description'    => t('The pass'),
        '#required'       => TRUE
      ];
      $form['api_name']=[
        '#title'          => t('Api name '),
        '#type'           => 'hidden',
        '#maxlength'      => 100,
        '#size'           => 100,
        '#default_value'  =>  $this->api_name,
        '#description'    => t('Api username'),
        '#required'       => TRUE
      ];
      $form['api_callback']=[
        '#title'          => t('Api callback '),
        '#type'           => 'hidden',
        '#maxlength'      => 100,
        '#size'           => 100,
        '#default_value'  =>  $this->api_callback,
        '#description'    => t('Api callback'),
        '#required'       => TRUE
      ];
      $form['return']=[
        '#title'          => t('Return url '),
        '#type'           => 'hidden',
        '#maxlength'      => 100,
        '#size'           => 100,
        '#default_value'  => $this->return_url,
        '#description'    => t('Return URL'),
        '#required'       => TRUE
      ];
      $form['phone_number']=[
        '#title'          => t('Phone Number'),
        '#type'           => 'textfield',
        '#maxlength'      => 10,
        '#size'           => 20,
        '#ajax'           => [
                'callback'        => '::send_stk',
                'disable-refocus' => FALSE,
                'event'           => 'change',
                'wrapper'         => 'resp_btn',
                'progress'        => [
                                  'type' => 'throbber',
                                  'message' => $this->t('Sending prompt...'),
                ]
        ],
        '#placholder'     => t('e.g 0700000000'),
        '#description'    => t('Provide your mobile number'),
        '#suffix'         => '<div><a href="#" class="btn btn-primary button button--primary btn-prompt">Send prompt</a></div>',
        '#required'       => TRUE,
      ];
      $form['link'] = [
        '#type' => 'item',
        '#prefix' => '<div id="resp_btn">',
        '#suffix' => '</div>',
      ];

      $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = [
          '#type' => 'submit',
          '#button_type' => 'primary',
          '#default_value' => $this->t('submit') ,
      ];

      $form['#attached']['library'][]='commerce_mpesa/commerce_mpesa_styling';
      $form['#theme'] = 'mpesa_tpl';

      return $form;
    }
  /**
     * {@inheritdoc}
     */
  public function validateForm(array & $form, FormStateInterface $form_state) {

    $phoneNum = $form_state->getValue('phone_number');

    if (strlen($phoneNum) < 11) {
      if (!substr($phoneNum, 0, 1) === '0') {
        $form_state->setErrorByName('phone_number', $this->t('Invalid Number format'));
      }
    }
    else {
      $form_state->setErrorByName('phone_number', $this->t('Invalid Number format'));
    }
  }

  /**
     * @inheritDoc
     */
  public function submitForm(array &$form, FormStateInterface $form_state) {
         //TODO
  }
  /**
   * Format mobile number
   * @return string formatted number
   *
   */
   private function format_stk_number($rawNum)
    {
      $rawNum=str_replace(' ', '',$rawNum); //remove spaces
      $number = substr_replace($rawNum, '254', 0, 1); //remove 0

      return $number;
    }
  /**
   * Get an Mpesa token
   *
   * @return string token
   */
  private function mpesa_token($auth_name,$auth_pass){
      try {
            $request = $this->client->get(self::MPESA_STAGING_URL['token_url'],
              ['auth' => [$auth_name, $auth_pass]]);
            $res_code = $request->getStatusCode();

            if ($res_code == 200) {
              $response = json_decode($request->getBody()->getContents(), true);
              $access_token = $response['access_token'];
              return $access_token;
            } else {
              return false;
            }
      }catch(\Exception $e){
            //log this
           \Drupal::logger('Commerce Mpesa')->error($e->getMessage());
           $this->messenger()->addError($this->t('Sorry this service is unavailable.. token'));
      }
    return false;
  }

  /**
   * Send an STK prompt
   * @return null
   */
  private function mpesa_stk($shortcode,$passkey,$pay_amount,$stk_num,$bearer_token, $callback){
     //change phone number
     $stk_num=$this->format_stk_number($stk_num);
     $http=\Drupal::request()->getSchemeAndHttpHost();

     $callbackurl=  $http .''. Url::fromRoute('commerce_payment.notify', array('commerce_payment_gateway'=> $callback))->toString();

      try{
            $timestamp = '20' . date("ymdhis");
            $stk_req = $this->client->post(self::MPESA_STAGING_URL['stk_url'], [
              'headers' => [
                'Authorization' => 'Bearer ' . $bearer_token,
              ],
              'json' => [
                "BusinessShortCode" => $shortcode,
                "Password"          => (string)base64_encode($shortcode . $passkey . $timestamp),
                "Timestamp"         => $timestamp,
                "TransactionType"   => "CustomerPayBillOnline",
                "Amount"            => $pay_amount,
                "PartyA"            => $stk_num,
                "PartyB"            => $shortcode,
                "PhoneNumber"       => $stk_num,
                "CallBackURL"       => "https://mikekebs.ilearn.world/stk/remotepost.php",
                "AccountReference"  => "iLearn.world",
                "TransactionDesc"   => "Course buy"
              ]
        ]);
        $response_stk = json_decode($stk_req->getBody()->getContents(), true);
        \Drupal::logger('Commerce Mpesa')->info('STK prompted successful for:'. $stk_num );
            //confirm payment
        if($response_stk['ResultCode']==0){
            //insert into db
            $this->checkoutId=$response_stk['CheckoutRequestID'];
            //$this->mpesatbl_insert($response_stk);
        }

      }catch(\Exception $e){
          \Drupal::logger('Commerce Mpesa')->error($e->getMessage());
          $this->messenger()->addError($this->t('Sorry this service is unavailable ...Mpesa stk'));
      }
  }

  private function mpesatbl_insert($res){
      $date= new \DateTime();
      try {
        //Db
        $db_con = \Drupal::service('database');
        $query = $db_con->insert('commerce_mpesa_ref')->fields(
          [
            'merchantRequestID' => $res['MerchantRequestID'],
            'checkoutRequestID' => $res['CheckoutRequestID'],
            'resp_code' => $res['ResponseCode'],
            'resp_desc ' => $res['ResponseDescription'],
            'timestamp' => $date->getTimestamp()
          ]
        )->execute();
      }catch (\Exception $e){
        \Drupal::logger('Commerce Mpesa')->error($e->getMessage());
      }
    }

  public function send_stk(array &$form, FormStateInterface $form_state) {

         $shortcode=$form_state->getValue('shortcode');
         $amount=$form_state->getValue('amount');
         $passkey=$form_state->getValue('passkey');
         $api_pass=$form_state->getValue('api_pass');
         $api_name=$form_state->getValue('api_name');
         $phone_number=$form_state->getValue('phone_number');
         $callback=$form_state->getValue('api_callback');
         //token
           $token=$this->mpesa_token($api_name,$api_pass);
          $this->mpesa_stk($shortcode,$passkey,$amount,$phone_number,$token,$callback);

          $onreturn_url=$this->return_url .'?'. $this->checkoutId;
    $form['output']['#prefix']= '<a href="'.$onreturn_url.'" class="btn btn-success">Verify Payment</a>';
    return $form['output'];
  }


}
