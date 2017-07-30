<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses_payment_m extends My_Model {

  public $fields = array(
      "company_id" => array(
          'label' => 'Company',
          'rules' => array('required')
      ),
      "category_id" => array(
          'value' => 0,
      ),
      "product_id" => array(
          'label' => 'Product',
          'rules' => array('required')
      ),
  );

  private function set_filter($company_id, $category_id, $payment_account, $payment_method_id, $start_date, $end_date, $search) {
    $this->db->reset_query();
    $this->db->from("payment_histories");
    $this->db->join('expenses_payments', 'payment_histories.detail_id = expenses_payments.`id`');
    $this->db->join('expenses_companies', 'expenses_payments.company_id = expenses_companies.`id`');
    $this->db->join('expenses_products', 'expenses_payments.product_id = expenses_products.`id`');
    $this->db->join('expenses_categories', 'expenses_products.category_id = expenses_categories.`id`');
    $this->db->join('payment_methods', 'payment_histories.payment_method_id = payment_methods.`id`');
    if ($search) {
      $this->db->like("expenses_products.`name`", $search);
    }
    if ($company_id != 'all') {
      $this->db->where("expenses_payments.company_id", $company_id);
    }
    if ($category_id != 'all') {
      $this->db->where("expenses_products.category_id", $category_id);
    }
    if ($payment_account != 'all') {
      if ($payment_method_id != 'all') {
        $this->db->where("payment_histories.payment_method_id", $payment_method_id);
      } else {
        $this->db->where("payment_methods.`type`", $payment_account);
      }
    }
    if ($start_date) {
      $this->db->where("payment_histories.pay_date >=", $start_date);
    }
    if ($end_date) {
      $this->db->where("payment_histories.pay_date <=", $end_date);
    }
    $this->db->where("payment_histories.type", PAYMENT_TYPE_EXPENSES);
    $this->db->where("payment_histories.reason_type", PAYMENT_REASON_TYPE_EXPENSES);
  }

  public function find($company_id, $category_id, $payment_account, $payment_method_id, $start_date, $end_date, $search, $orders, $start, $length) {
    $this->set_filter("all", "all", "all", "all", false, false, "");
    $this->db->select("count(payment_histories.`id`) as history_count, sum(payment_histories.amount) as sum_amount");
    $all_count = $this->db->count_all_results();
    if ($all_count == 0) {
      return array(
          "total" => $all_count,
          "amount" => 0,
          "count" => 0,
          "data" => array()
      );
    }

    $this->set_filter($company_id, $category_id, $payment_account, $payment_method_id, $start_date, $end_date, $search);
    $this->db->select("count(payment_histories.`id`) as history_count, sum(payment_histories.amount) as sum_amount");
    $result = $this->db->get()->result();
    if ($result[0]->history_count == 0) {
      return array(
          "total" => $all_count,
          "count" => $result[0]->history_count,
          "amount" => $result[0]->sum_amount,
          "data" => array()
      );
    }

    $this->set_filter($company_id, $category_id, $payment_account, $payment_method_id, $start_date, $end_date, $search);
    $this->db->select("payment_histories.`id`, payment_histories.pay_date, payment_histories.pay_time, payment_histories.payment_method_id, payment_histories.amount, "
        . "expenses_companies.`name` as company, expenses_categories.`name` as category, expenses_products.`name` as product");
    if ($orders) {
      foreach ($orders as $order) {
        if ($order['column'] == 1) {
          $this->db->order_by("pay_date", $order['dir']);
          $this->db->order_by("pay_time", $order['dir']);
        } else {
          switch ($order['column']) {
            case 2 :
              $order_field = "payment_histories.payment_method_id";
              break;
            case 3 :
              $order_field = "payment_histories.amount";
              break;
            case 4 :
              $order_field = "expenses_companies.`name`";
              break;
            case 5 :
              $order_field = "expenses_categories.`name`";
              break;
            case 6 :
              $order_field = "expenses_products.`name`";
              break;
          }
          $this->db->order_by($order_field, $order['dir']);
        }
      }
    } else {
      $this->db->order_by("payment_histories.`id`");
    }
    if ($length > 0 && $start >= 0) {
      $this->db->limit($length, $start);
    }
    return array(
        "total" => $all_count,
        "count" => $result[0]->history_count,
        "amount" => $result[0]->sum_amount,
        "data" => $this->db->get()->result()
    );
  }

}
