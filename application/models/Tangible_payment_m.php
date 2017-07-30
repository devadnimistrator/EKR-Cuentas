<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tangible_payment_m extends My_Model {

  public $fields = array(
      "category_id" => array(
          'value' => 0,
      ),
      "product_id" => array(
          'label' => 'Product',
          'rules' => array('required')
      ),
  );

}
