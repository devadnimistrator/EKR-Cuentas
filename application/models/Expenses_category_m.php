<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses_category_m extends My_Model {

  public $fields = array(
      'name' => array(
          'label' => 'Name',
          'rules' => array('required', 'min_length[3]')
      )
  );

  public function get_categories($index_array = true) {
    $result = $this->db->order_by("name")->get($this->table)->result();

    if (empty($result)) {
      return array();
    } else {
      if ($index_array) {
        $categories = array();
        foreach ($result as $category) {
          $categories[$category->id] = $category->name;
        }
        return $categories;
      } else {
        return $result;
      }
    }
  }
  
  public function delete() {
    parent::delete();
    
    sync_expenses_payments();
  }

}
