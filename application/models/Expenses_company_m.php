<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses_company_m extends My_Model {

  public $fields = array(
      "taxnumber" => array(
          'label' => 'RFC',
          'rules' => array('required')
      ),
      "name" => array(
          'label' => 'Name',
          'rules' => array('required')
      ),
      'street' => array(
          'label' => 'Street'
      ),
      "street_number" => array(
          'label' => 'Street Number'
      ),
      'suburb' => array(
          'label' => 'Suburb'
      ),
      'state' => array(
          'label' => 'State'
      ),
      'city' => array(
          'label' => 'City'
      ),
      'zipcode' => array(
          'label' => 'Zipcode'
      ),
      'telphone' => array(
          'type' => 'tel',
          'label' => 'Telphone',
          'rules' => array('required', 'min_length[8]')
      ),
      'email' => array(
          'type' => 'email',
          'label' => 'Email',
          'rules' => array(
              'required',
              'valid_email')
      )
  );

  public function display() {
    $address = $this->street_number . " " . $this->street . "<br>";
    if ($this->suburb) {
      $address.= $this->suburb . ", ";
    }
    $address.= $this->city . ", " . $this->state . " " . $this->zipcode . "<br>";

    return $address;
  }

  private function set_filter($search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($search != '') {
      $this->db->like("taxnumber", $search);
      $this->db->or_like("name", $search);
      $this->db->or_like("street", $search);
      $this->db->or_like("street_number", $search);
      $this->db->or_like("state", $search);
      $this->db->or_like("city", $search);
      $this->db->or_like("zipcode", $search);
      $this->db->or_like("telphone", $search);
      $this->db->or_like("email", $search);
    }
  }

  public function find($search, $orders, $start, $length) {
    $all_count = $this->count_all();
    if ($all_count == 0) {
      return array(
          "total" => $all_count,
          "count" => 0,
          "data" => array()
      );
    }

    $this->set_filter($search);
    $count_by_filter = $this->db->count_all_results();
    if ($count_by_filter == 0) {
      return array(
          "total" => $all_count,
          "count" => $count_by_filter,
          "data" => array()
      );
    }

    $this->set_filter($search);
    foreach ($orders as $order) {
      switch ($order['column']) {
        case 1 :
          $order_field = "name";
          break;
        case 2 :
          $order_field = "taxnumber";
          break;
        case 3 :
          $order_field = "telphone";
          break;
        case 4 :
          $order_field = "email";
          break;
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

  public function get_companies($index_array = true) {
    $result = $this->db->order_by("name")->get($this->table)->result();

    if (empty($result)) {
      return array();
    } else {
      if ($index_array) {
        $companies = array();
        foreach ($result as $company) {
          $companies[$company->id] = $company->name;
        }
        return $companies;
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
