<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Chart extends My_Controller {

  public function __construct() {
    parent::__construct();
    
    $this->check_admin();

    $this->page_title = __("Reports");
  }

  public function index() {
    $this->load->view('reports/dashboard');
  }

  public function cash() {
    $account = $this->uri->segment(4);
    $start_date = $this->uri->segment(5);
    $end_date = $this->uri->segment(6);
    if (!$account) {
      $account = 'all';
    }
    if (!$start_date) {
      $start_date = date('M d, y');
    } else {
      $start_date = my_formart_date($start_date, 'M d, y');
    }
    if (!$end_date) {
      $end_date = my_add_date(-29, false, 'M d, y');
    } else {
      $end_date = my_formart_date($end_date, 'M d, y');
    }

    $params = array("account" => $account, "start_date" => $start_date, "end_date" => $end_date);
    
    $this->load->view('reports/chart/header', $params);
    $this->load->view('reports/chart/cash', $params);
    $this->load->view('reports/chart/income', $params);

    $this->load->view('js/daterange');
    $this->load->view('js/echart');
  }

  public function ajax_get_cash() {
    $this->load->model("payment_method_m");

    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    $account = $this->input->post('account');

    $this->load->library("my_date");
    $this->my_date->between_dates($start_date, $end_date);

    $result = array(
        "dates" => array(),
        "income" => array(),
        "expenses" => array(),
        "remaining" => [],
        "before_remaining" => 0,
        "all_income" => 0,
        "all_expenses" => 0
    );

    $remaining = 0;
    $payments = $this->payment_method_m->get_methods(true, $account);
    $payments = implode(",", array_keys($payments));

    $query = "select sum(IF(`type`=" . PAYMENT_TYPE_INCOME . ", amount, 0-amount)) as sum_amount from payment_histories where 1=1";
    if ($this->my_date->result->type == 'D') {
      $query.= " and pay_date < '" . $this->my_date->result->dates[0] . "'";
    } else {
      $query.= " and pay_date < '" . $this->my_date->result->dates[0] . "-01'";
    }
    if ($account != 'all') {
      $query.= " and payment_method_id in (" . $payments . ")";
    }

    $amounts = $this->db->query($query)->result();
    if (!empty($amounts)) {
      foreach ($amounts as $amount) {
        $remaining = $amount->sum_amount;
      }
    }
    $result["before_remaining"] = $remaining;


    $query = "select `type`, sum(amount) as sum_amount from payment_histories where 1=1 %s group by `type`";
    foreach ($this->my_date->result->dates as $date) {
      $income = 0;
      $expenses = 0;

      $where = "";
      if ($this->my_date->result->type == 'D') {
        $where .= " and pay_date='" . $date . "'";
      } else {
        $where .= " and pay_date like '" . $date . "-%'";
      }
      if ($account != 'all') {
        $where .= " and payment_method_id in (" . $payments . ")";
      }

      $amounts = $this->db->query(sprintf($query, $where))->result();

      foreach ($amounts as $amount) {
        if ($amount->type == PAYMENT_TYPE_INCOME) {
          $income = $amount->sum_amount;
        } elseif ($amount->type == PAYMENT_TYPE_EXPENSES) {
          $expenses = $amount->sum_amount;
        }
      }
      $remaining += ($income - $expenses);

      if ($this->my_date->result->type == 'D') {
        $result['dates'][] = my_formart_date($date, "j");
      } elseif ($this->my_date->result->type == 'M') {
        $result['dates'][] = my_formart_date($date, "M, y");
      }

      $result['income'][] = $income;
      $result['expenses'][] = $expenses;
      $result['remaining'][] = $remaining;

      $result['all_income'] += $income;
      $result['all_expenses'] += $expenses;
    }


    header('Content-Type: application/json');
    echo json_encode($result);
  }

  public function ajax_get_income() {
    $this->load->model("payment_method_m");

    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    $account = $this->input->post('account');

    $this->load->library("my_date");
    $this->my_date->between_dates($start_date, $end_date);

    $result = array(
        "dates" => array(),
        "paid" => array(),
        "pending" => array(),
        "all_paid" => 0,
        "all_pending" => 0
    );

    $remaining = 0;
    $payments = $this->payment_method_m->get_methods(true, $account);
    $payments = implode(",", array_keys($payments));

    $query = "select `status`, sum(amount) as sum_amount from payment_histories where `type`=" . PAYMENT_TYPE_INCOME . " %s group by `status`";
    foreach ($this->my_date->result->dates as $date) {
      $paid = 0;
      $pending = 0;

      $where = "";
      if ($this->my_date->result->type == 'D') {
        $where .= " and pay_date='" . $date . "'";
      } else {
        $where .= " and pay_date like '" . $date . "-%'";
      }
      if ($account != 'all') {
        $where .= " and payment_method_id in (" . $payments . ")";
      }

      $amounts = $this->db->query(sprintf($query, $where))->result();

      foreach ($amounts as $amount) {
        if ($amount->status == PAYMENT_STATUS_PAID) {
          $paid = $amount->sum_amount;
        } elseif ($amount->status == PAYMENT_STATUS_PENDING) {
          $pending = $amount->sum_amount;
        }
      }

      if ($this->my_date->result->type == 'D') {
        $result['dates'][] = my_formart_date($date, "j");
      } elseif ($this->my_date->result->type == 'M') {
        $result['dates'][] = my_formart_date($date, "M, y");
      }

      $result['paid'][] = $paid;
      $result['pending'][] = $pending;

      $result['all_paid'] += $paid;
      $result['all_pending'] += $pending;
    }


    header('Content-Type: application/json');
    echo json_encode($result);
  }

}
