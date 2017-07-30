<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Student_m extends My_Model {

  public $fields = array(
      'first_name' => array(
          'label' => 'First Name',
          'rules' => array('required', 'min_length[3]')
      ),
      'last_name' => array(
          'label' => 'Last Name',
          'rules' => array('required', 'min_length[3]')
      ),
      'email' => array(
          'label' => 'Email',
          'type' => 'email',
          'rules' => array(
              'required',
              'min_length[6]',
              'valid_email')
      ),
      'phone' => array(
          'label' => 'Phone Number',
      ),
      'cellphone' => array(
          'label' => 'Cell Phone',
      ),
      'company_name' => array(
          'label' => 'Company Name',
      ),
      'tax_payer_number' => array(
          'label' => 'Tax Payer Number',
      ),
      'address' => array(
          'label' => 'Address',
      ),
      'city' => array(
          'label' => 'City'
      ),
      'state' => array(
          'label' => 'State'
      ),
      'zipcode' => array(
          'label' => 'Zipcode'
      ),
      'invoice_email' => array(
          'label' => 'Invoice Email',
      )
  );

  private function set_filter($search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    if ($search != '') {
      $this->db->group_start();
      $this->db->or_like("first_name", $search);
      $this->db->or_like("last_name", $search);
      $this->db->or_like("email", $search);
      $this->db->or_like("phone", $search);
      $this->db->or_like("cellphone", $search);
      $this->db->group_end();
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
    if ($orders) {
      foreach ($orders as $order) {
        switch ($order['column']) {
          case 1 :
            $order_field = "first_name";
            break;
          case 2 :
            $order_field = "last_name";
            break;
          case 3 :
            $order_field = "email";
          case 4 :
            $order_field = "phone";
          case 5 :
            $order_field = "cellphone";
            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("first_name");
      $this->db->order_by("last_name");
    }
    $this->db->limit($length, $start);
    return array(
        "total" => $all_count,
        "count" => $count_by_filter,
        "data" => $this->db->get()->result()
    );
  }
  
  public function get_name() {
    return $this->first_name . " " . $this->last_name;
  }
  
  public function delete() {
    parent::delete();
    
    sync_education_payments();
  }

}
