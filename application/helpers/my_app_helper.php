<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function my_get_payment_methods() {
  $CI = &get_instance();

  $methods = $CI->config->item("paymnet_method_types");

  foreach ($methods as $key => $value) {
    $methods[$key] = __($value);
  }

  return $methods;
}

function my_get_income_types() {
  $types = array(
      PAYMENT_REASON_TYPE_EDUCATION => __("Education"),
      PAYMENT_REASON_TYPE_TANGIBLE => __("Tangible"),
      PAYMENT_REASON_TYPE_MOVEMENT => __("Movement"),
      PAYMENT_REASON_TYPE_INCOME => __("Other")
  );

  return $types;
}

function my_get_expenses_types() {
  $types = array(
      PAYMENT_REASON_TYPE_EXPENSES => __("Products"),
      PAYMENT_REASON_TYPE_MOVEMENT => __("Movement")
  );

  return $types;
}

function __($line, $_ = NULL) {
  $args = func_get_args();
  
  $CI = &get_instance();
  
  if (is_array($line)) {
    $result = array();

    foreach ($line as $key => $value) {
      $args[0] = $value;

      $result[$key] = call_user_func_array(array($CI->my_translator, "translate"), $args);
    }

    return $result;
  } else {
    return call_user_func_array(array($CI->my_translator, "translate"), $args);
  }
}

function ___($line, $_ = NULL) {
  $args = func_get_args();
  
  echo call_user_func_array("__", $args);
}

function sync_expenses_payments() {
  $CI = &get_instance();
  
  $CI->db->query("delete from payment_histories where detail_id not in (select `id` from expenses_payments) and `type`='".PAYMENT_TYPE_EXPENSES."' and `reason_type`='".PAYMENT_REASON_TYPE_EXPENSES."'");
}

function sync_tangible_payments() {
  $CI = &get_instance();
  
  $CI->db->query("delete from payment_histories where detail_id not in (select `id` from tangible_payments) and `type`='".PAYMENT_TYPE_INCOME."' and `reason_type`='".PAYMENT_REASON_TYPE_TANGIBLE."'");
}

function sync_education_payments() {
  $CI = &get_instance();
  
  $CI->db->query("delete from payment_histories where detail_id not in (select `id` from education_payments) and `type`='".PAYMENT_TYPE_INCOME."' and `reason_type`='".PAYMENT_REASON_TYPE_EDUCATION."'");
}