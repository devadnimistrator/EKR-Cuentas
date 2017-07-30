<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_method_m extends My_Model {

  public $fields = array(
      'name' => array(
          'label' => 'Name',
          'rules' => array('required', 'min_length[3]')
      ),
      'type' => array(
          'label' => 'Type',
          'rules' => array('required')
      )
  );

  public function get_methods($index_array = true, $type = 'all') {
    if ($type != 'all') {
      $this->db->where("type", $type);
    }
    $result = $this->db->order_by("type")->order_by("name")->get($this->table)->result();

    if (empty($result)) {
      return array();
    } else {
      if ($index_array) {
        $methods = array();
        foreach ($result as $method) {
          $methods[$method->id] = $method->name;
        }
        return $methods;
      } else {
        return $result;
      }
    }
  }

}
