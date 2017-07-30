<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses_product_m extends My_Model {

  public $fields = array(
      "category_id" => array(
          'label' => 'Category',
          'rules' => array('required')
      ),
//      "price" => array(
//          'type' => 'number',
//          'label' => 'Price',
//          'rules' => array('required')
//      ),
      'name' => array(
          'label' => 'Name',
          'rules' => array('required', 'min_length[5]')
      )
  );

  private function set_filter($category, $search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($search != '') {
      $this->db->like("name", $search);
    }
    if ($category != 'all') {
      $this->db->where("category_id", $category);
    }
  }

  public function find($category, $search, $orders, $start, $length) {
    $all_count = $this->count_all();
    if ($all_count == 0) {
      return array(
          "total" => $all_count,
          "count" => 0,
          "data" => array()
      );
    }

    $this->set_filter($category, $search);
    $count_by_filter = $this->db->count_all_results();
    if ($count_by_filter == 0) {
      return array(
          "total" => $all_count,
          "count" => $count_by_filter,
          "data" => array()
      );
    }

    $this->set_filter($category, $search);
    foreach ($orders as $order) {
      switch ($order['column']) {
        case 1 :
          $order_field = "category_id";
          break;
        case 2 :
          $order_field = "name";
          break;
//        case 3 :
//          $order_field = "price";
//          break;
      }
      $this->db->order_by($order_field, $order['dir']);
    }
    $this->db->limit($length, $start);
    return array(
        "total" => $all_count,
        "count" => $count_by_filter,
        "data" => $this->db->get()->result()
    );
  }

  public function get_products($category_id = 0, $index_array = true) {
    if ($category_id > 0) {
      $this->db->where("category_id", $category_id);
    }
    $result = $this->db->order_by("name")->get($this->table)->result();

    if (empty($result)) {
      return array();
    } else {
      if ($index_array) {
        $products = array();
        foreach ($result as $product) {
//          $products[$product->id] = my_get_currency_unit(DEFAULT_CURRENCY_UNIT, 'text') . $product->price . " - " . $product->name;
          $products[$product->id] = $product->name;
        }
        return $products;
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
