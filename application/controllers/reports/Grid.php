<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Grid extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->check_admin();
    
    $this->page_title = __("Reports");
  }

  public function index() {
    redirect("reports/grid/cash");
  }

  public function cash() {
    $account = $this->uri->segment(4);
    
    $start_date = my_add_date(-29, false, 'M d, y');
    $end_date = my_formart_date(false, 'M d, y');

    $params = array("account" => $account, "start_date" => $start_date, "end_date" => $end_date);

    $this->load->view('reports/grid/cash', $params);
    $this->load->view('js/daterange');
  }

  public function ajax_get_cash() {
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    $account = $this->input->post('account');

    $returnData = $this->get_report_data($start_date, $end_date, $account, DATE_FULL_FORMAT);

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_export_cash() {
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    $account = $this->input->get('account');

    $returnData = $this->get_report_data($start_date, $end_date, $account, 'Y-m-d');
    $payments = array();
    foreach ($returnData['data'] as $payment) {
      $payments[] = array(
          $payment['date'],
          strip_tags($payment['income']),
          strip_tags($payment['expenses']),
          strip_tags($payment['remaining'])
      );
    }
    $payments[] = array(" ", " ", " ", " ");
    $payments[] = array(__("Before Remaining"), __("Sum Income"), __("Sum Expenses"), __("Last Remaining"));
    $payments[] = array($returnData['before_remaining'], $returnData['sum_income'], $returnData['sum_expenses'], $returnData['sum_remaining']);

    $currency_unit = my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string");

    $this->load->library("my_export");
    $haders = array(
        __("Date"),
        __("Incomed(%s)", $currency_unit),
        __("Expenses(%s)", $currency_unit),
        __("Remaining(%s)", $currency_unit)
    );
    $this->my_export->toCSV("report(" . $start_date . " to " . $end_date . ")", $haders, $payments);
  }

  private function get_report_data($start_date, $end_date, $account, $date_format) {
    $returnData = array(
        "data" => array(),
        "before_remaining" => 0,
        "sum_income" => 0,
        "sum_expenses" => 0,
        "sum_remaining" => 0
    );

    $remaining = 0;
    $payments = $this->payment_method_m->get_methods(true, $account);
    $payments = implode(",", array_keys($payments));

    $this->load->library("my_date");
    $this->my_date->between_dates($start_date, $end_date);

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
        $remaining = $amount->sum_amount * 1;
      }
    }
    $returnData["before_remaining"] = $remaining;

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
        $format_date = my_formart_date($date, $date_format);
      } elseif ($this->my_date->result->type == 'M') {
        $format_date = my_formart_date($date, "Y-m");
      }

      $returnData["data"][] = array(
          "date" => $format_date,
          "income" => $income == 0 ? '' : '<a href="javascript:showDetailCash(\'' . $date . '\', ' . PAYMENT_TYPE_INCOME . ', \'' . __('Income') . '\')"><span class = "datatable-number">' . number_format($income, 2) . '</span></a>',
          "expenses" => $expenses == 0 ? '' : '<a href="javascript:showDetailCash(\'' . $date . '\', ' . PAYMENT_TYPE_EXPENSES . ', \'' . __('Expenses') . '\')"><span class = "datatable-number">' . number_format($expenses, 2) . '</span></a>',
          "remaining" => $remaining == 0 ? '' : '<span class = "datatable-number">' . number_format($remaining, 2) . '</span>'
      );

      $returnData['sum_income'] += $income;
      $returnData['sum_expenses'] += $expenses;
      $returnData['sum_remaining'] = $remaining;
    }

    return $returnData;
  }

}
