<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_history_m extends My_Model {

  public $fields = array(
      "payment_method_id" => array(
          'label' => 'Payment',
          'rules' => array('required')
      ),
      "pay_date" => array(
          'label' => 'Date',
          'rules' => array('required'),
      ),
      "pay_time" => array(
          'label' => 'Time'
      ),
      "amount" => array(
          'label' => 'Amount',
          'type' => 'number',
          'rules' => array('required')
      ),
      'reason' => array(
          'label' => 'Reason'
      ),
      'reason_desc' => array(
          'type' => 'textarea',
          'label' => 'Description'
      )
  );

  public function __construct($id = 0) {
    $this->fields['pay_date']['default'] = date('Y-m-d');
    $this->fields['pay_time']['default'] = date('H:i');

    parent::__construct($id);
  }

  private function set_filter($inout_type, $payment_account, $payment_method_id, $reason_type, $reason, $category_id, $start_date, $end_date, $payment_status, $company_id) {
    $this->db->reset_query();
    $this->db->from("payment_histories");
    $this->db->join('payment_methods', 'payment_histories.payment_method_id = payment_methods.`id`');
    if ($reason) {
      $this->db->like("payment_histories.reason_desc", $reason);
    }
    if ($payment_account != 'all') {
      if ($payment_method_id != 'all') {
        $this->db->where("payment_histories.payment_method_id", $payment_method_id);
      } else {
        $this->db->where("payment_methods.`type`", $payment_account);
      }
    }
    if ($reason_type != 'all') {
      if ($category_id && $category_id != 'all') {
        if ($reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
          $where = "payment_histories.detail_id"
              . " in (select `id` from education_payments where class_id=" . $category_id . ")";
          $this->db->where($where);
        } elseif ($reason_type == PAYMENT_REASON_TYPE_TANGIBLE) {
          $where = "payment_histories.detail_id"
              . " in (select `id` from tangible_payments where product_id"
              . " in (select `id` from tangible_products where category_id=" . $category_id . "))";
          $this->db->where($where);
        } elseif ($reason_type == PAYMENT_REASON_TYPE_EXPENSES) {
          $where = "payment_histories.detail_id"
              . " in (select `id` from expenses_payments where product_id"
              . " in (select `id` from expenses_products where category_id=" . $category_id . "))";
          $this->db->where($where);
        }
      }

      $this->db->where("payment_histories.reason_type", $reason_type);
    }
    if ($reason_type == PAYMENT_REASON_TYPE_EXPENSES && $company_id != 'all') {
      $where = "payment_histories.detail_id"
          . " in (select `id` from expenses_payments where product_id"
          . " in (select `id` from expenses_products where company_id=" . $company_id . "))";
      $this->db->where($where);
    }

    $this->db->where("payment_histories.type", $inout_type);
    if ($payment_status != 'all') {
      $this->db->where("payment_histories.status", $payment_status);
    }
    if ($start_date) {
      $this->db->where("payment_histories.pay_date >=", $start_date);
    }
    if ($end_date) {
      $this->db->where("payment_histories.pay_date <=", $end_date);
    }
  }

  public function find($inout_type, $payment_account, $payment_method_id, $reason_type, $reason, $category_id, $start_date, $end_date, $payment_status, $orders, $start, $length, $company_id = 'all') {
    $this->set_filter($inout_type, "all", "all", "all", "", "all", false, false, "all", "all");
    $this->db->select("count(payment_histories.`id`) as history_count, sum(payment_histories.amount) as sum_amount");
    $all_count = $this->db->count_all_results();
    if ($all_count == 0) {
      return array(
          "total" => $all_count,
          "amount" => 0,
          "paid" => 0,
          "count" => 0,
          "data" => array()
      );
    }

    $this->set_filter($inout_type, $payment_account, $payment_method_id, $reason_type, $reason, $category_id, $start_date, $end_date, $payment_status, $company_id);
    $this->db->select("count(payment_histories.`id`) as history_count, sum(payment_histories.amount) as sum_amount, sum(payment_histories.paid_amount) as sum_paid");
    $result = $this->db->get()->result();

    if ($result[0]->history_count == 0) {
      return array(
          "total" => $all_count,
          "count" => $result[0]->history_count,
          "amount" => $result[0]->sum_amount,
          "paid" => $result[0]->sum_paid,
          "data" => array()
      );
    }

    $this->set_filter($inout_type, $payment_account, $payment_method_id, $reason_type, $reason, $category_id, $start_date, $end_date, $payment_status, $company_id);
    $this->db->select("payment_histories.*");
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
        "paid" => $result[0]->sum_paid,
        "data" => $this->db->get()->result()
    );
  }

}
