<?php


/**
 * Implements hooks_views_data()
 */

function commerce_mpesa_views_data(){
    $data = array();

    $data['commerce_mpesa_ipn']['table']['group'] = t('commerce_mpesa_ipn');

    $data['commerce_mpesa_ipn']['table']['provider'] = 'Mpesa';

    // Views UI
    $data['commerce_mpesa_ipn']['table']['base']= array(
      'field' => 'id',
      'title' => t('Mpesa Payments'),
      'help' => t('Data View for all Mpesa payments'),
      'weight' => -10,
    );

  $data['commerce_mpesa_ipn']['phone_num'] = array(
    'title' => t('Mpesa Phone number'),
    'help' => t('The number that paid.'),
    'field' => array(
      // ID of field handler plugin to use.
      'id' => 'standard',
    ),
    'sort' => array(
      // ID of sort handler plugin to use.
      'id' => 'standard',
    ),
    'filter' => array(
      // ID of filter handler plugin to use.
      'id' => 'string',
    ),
    'argument' => array(
      // ID of argument handler plugin to use.
      'id' => 'string',
    ),
  );
  $data['commerce_mpesa_ipn']['merchantRequestID'] = array(
    'title' => t('MerchantRequestID'),
    'help' => t('The Merchant request ID used.'),
    'field' => array(
      // ID of field handler plugin to use.
      'id' => 'standard',
    ),
    'sort' => array(
      // ID of sort handler plugin to use.
      'id' => 'standard',
    ),
    'filter' => array(
      // ID of filter handler plugin to use.
      'id' => 'string',
    ),
    'argument' => array(
      // ID of argument handler plugin to use.
      'id' => 'string',
    ),
  );
  $data['commerce_mpesa_ipn']['checkoutRequestID'] = array(
    'title' => t('checkoutRequestID'),
    'help' => t('The checkoutRequest ID used.'),
    'field' => array(
      // ID of field handler plugin to use.
      'id' => 'standard',
    ),
    'sort' => array(
      // ID of sort handler plugin to use.
      'id' => 'standard',
    ),
    'filter' => array(
      // ID of filter handler plugin to use.
      'id' => 'string',
    ),
    'argument' => array(
      // ID of argument handler plugin to use.
      'id' => 'string',
    ),
  );
  $data['commerce_mpesa_ipn']['mpesa_ref_id'] = array(
    'title' => t('Mpesa reference code'),
    'help' => t('The Mpesa reference code.'),
    'field' => array(
      // ID of field handler plugin to use.
      'id' => 'standard',
    ),
    'sort' => array(
      // ID of sort handler plugin to use.
      'id' => 'standard',
    ),
    'filter' => array(
      // ID of filter handler plugin to use.
      'id' => 'string',
    ),
    'argument' => array(
      // ID of argument handler plugin to use.
      'id' => 'string',
    ),
  );
  $data['commerce_mpesa_ipn']['resp_desc'] = array(
    'title' => t('Mpesa response desc'),
    'help' => t('The Mpesa reference description.'),
    'field' => array(
      // ID of field handler plugin to use.
      'id' => 'standard',
    ),
    'sort' => array(
      // ID of sort handler plugin to use.
      'id' => 'standard',
    ),
    'filter' => array(
      // ID of filter handler plugin to use.
      'id' => 'string',
    ),
    'argument' => array(
      // ID of argument handler plugin to use.
      'id' => 'string',
    ),
  );

  $data['commerce_mpesa_ipn']['amount'] = array(
    'title' => t('Amount'),
    'help' => t('Paid amount.'),
    'field' => array(
      // ID of field handler plugin to use.
      'id' => 'standard',
    ),
    'sort' => array(
      // ID of sort handler plugin to use.
      'id' => 'standard',
    ),
    'filter' => array(
      // ID of filter handler plugin to use.
      'id' => 'string',
    ),
    'argument' => array(
      // ID of argument handler plugin to use.
      'id' => 'string',
    ),
  );

  $data['commerce_mpesa_ipn']['timestamp'] = array(
    'title' => t('Timestamp'),
    'help' => t('The timestamp.'),
    'field' => array(
      // ID of field handler plugin to use.
      'id' => 'standard',
    ),
    'sort' => array(
      // ID of sort handler plugin to use.
      'id' => 'standard',
    ),
    'filter' => array(
      // ID of filter handler plugin to use.
      'id' => 'string',
    ),
    'argument' => array(
      // ID of argument handler plugin to use.
      'id' => 'string',
    ),
  );

  return $data;
}
