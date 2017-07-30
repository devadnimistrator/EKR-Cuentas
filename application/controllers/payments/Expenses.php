<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Expenses");

    $this->expenses_types = my_get_expenses_types();

    $this->load->model('payment_history_m');
    $this->load->model("expenses_payment_m");

    $this->load->model('expenses_company_m');
    $this->load->model('expenses_category_m');
    $this->load->model("expenses_product_m");

    $this->companies = $this->expenses_company_m->get_companies();
    $this->categories = $this->expenses_category_m->get_categories();

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Payment");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->payment_history_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Payment") . ": #" . $this->payment_history_m->id;

      $this->expenses_payment_m->get_by_id($this->payment_history_m->detail_id);
      $this->expenses_product_m->get_by_id($this->expenses_payment_m->product_id);
      $this->expenses_payment_m->category_id = $this->expenses_product_m->category_id;
    }
  }

  public function index() {
    $this->payment_methods = $this->payment_method_m->get_methods(false);

    $this->load->library("my_bs_form");

    $this->my_save_current_url();

    $params = $this->uri->ruri_to_assoc();
    $input_params = array(
        "expenses_type" => isset($params['type']) ? $params['type'] : "all",
        "payment_account" => isset($params['account']) ? $params['account'] : "all"
    );
    $this->load->view("payments/expenses/list", $input_params);
    $this->load->view('js/daterange');
  }

  public function add() {
    $this->payment_methods = $this->payment_method_m->get_methods();

    $this->my_save_current_url();

    $this->load->library("my_bs_form");

    $error_msgs = array();
    if ($this->input->post('action') == 'process') {
      $error_msgs = $this->save_expenses();
      if ($error_msgs === false) {
        my_set_system_message(__("Successfully save expenses."), "success");

        redirect("payments/expenses/edit/" . $this->payment_history_m->id);
      }
    }

    $this->products = array();
    if ($this->expenses_payment_m->category_id) {
      $this->products = $this->expenses_product_m->get_products($this->expenses_payment_m->category_id);
    } else {
      if (!empty($this->categories)) {
        $cat_ids = array_keys($this->categories);
        $this->products = $this->expenses_product_m->get_products($cat_ids[0]);
      }
    }

    $this->load->view('payments/expenses/edit', array("error_msgs" => $error_msgs));
  }

  public function edit() {
    $this->payment_methods = $this->payment_method_m->get_methods();
    $this->my_save_current_url();

    $this->load->library("my_bs_form");

    $error_msgs = array();
    if ($this->input->post('action') == 'process') {
      $error_msgs = $this->save_expenses();
      if ($error_msgs === false) {
        my_set_system_message(__("Successfully save expenses."), "success");

        redirect("payments/expenses/edit/" . $this->payment_history_m->id);
      }
    }

    $this->products = array();
    if ($this->expenses_payment_m->category_id) {
      $this->products = $this->expenses_product_m->get_products($this->expenses_payment_m->category_id);
    } else {
      if (!empty($this->categories)) {
        $cat_ids = array_keys($this->categories);
        $this->products = $this->expenses_product_m->get_products($cat_ids[0]);
      }
    }

    $this->load->view('payments/expenses/edit', array("error_msgs" => $error_msgs));
  }

  private function save_expenses() {
    $error_msgs = false;

    $this->load->library("form_validation");
    $valid_config = array(
        array(
            'field' => 'pay_date',
            'label' => __('Registered'),
            'rules' => 'required'
        ),
        array(
            'field' => 'pay_time_h',
            'label' => __('Registered'),
            'rules' => 'required'
        ),
        array(
            'field' => 'pay_time_m',
            'label' => __('Registered'),
            'rules' => 'required'
        ),
        array(
            'field' => 'company_id',
            'label' => __('Company'),
            'rules' => 'required'
        ),
        array(
            'field' => 'category_id',
            'label' => __('Category'),
            'rules' => 'required'
        ),
        array(
            'field' => 'product_id',
            'label' => __('Product'),
            'rules' => 'required'
        ),
        array(
            'field' => 'amount',
            'label' => __('Amount'),
            'rules' => ('required|greater_than[0]'),
        )
    );

    $this->form_validation->set_rules($valid_config);

    $this->expenses_payment_m->company_id = $this->input->post("company_id");
    $this->expenses_payment_m->category_id = $this->input->post("category_id");
    $this->expenses_payment_m->product_id = $this->input->post("product_id");

    $this->payment_history_m->payment_method_id = $this->input->post("payment_method_id");
    $this->payment_history_m->pay_date = my_formart_date($this->input->post("pay_date"), 'Y-m-d');
    $this->payment_history_m->pay_time = $this->input->post("pay_time_h") . ":" . $this->input->post("pay_time_m");
    $this->payment_history_m->status = PAYMENT_STATUS_PAID;
    $this->payment_history_m->before_amount = $this->payment_history_m->amount = $this->payment_history_m->paid_amount = $this->input->post('amount');

    if ($this->form_validation->run() == FALSE) {
      $error_msgs = $this->form_validation->error_array();
    } else {
      $this->expenses_payment_m->save();

      $this->payment_history_m->type = PAYMENT_TYPE_EXPENSES;
      $this->payment_history_m->reason_type = PAYMENT_REASON_TYPE_EXPENSES;

      if ($this->expenses_payment_m->product_id > 0) {
        $this->expenses_product_m->get_by_id($this->expenses_payment_m->product_id);
        $this->payment_history_m->reason_desc = $this->expenses_product_m->name;
      }
      $this->payment_history_m->detail_id = $this->expenses_payment_m->id;

      if ($this->payment_history_m->save()) {
        
      } else {
        $error_msgs = array(__("Failed save expenses."));
      }
    }

    return $error_msgs;
  }

  public function ajax_find() {
    $this->payment_methods = $this->payment_method_m->get_methods();

    $expenses_type = $this->input->post('expenses_type');
    $company_id = $this->input->post('company_id');
    $category_id = $this->input->post('category_id');
    $payment_account = $this->input->post('payment_account');
    $payment_method = $this->input->post('payment_method');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');

    $payments = array();
    $result = $this->payment_history_m->find(PAYMENT_TYPE_EXPENSES, $payment_account, $payment_method, $expenses_type, $search['value'], $category_id, $start_date, $end_date, "all", $order, $start, $length, $company_id);
    if ($result['count'] > 0) {
      foreach ($result['data'] as $row) {
        $payment_method = "";
        if (isset($this->payment_methods[$row->payment_method_id])) {
          $payment_method = $this->payment_methods[$row->payment_method_id];
        }

        $amount_text = number_format($row->amount, 2);
        if ($row->amount != $row->before_amount) {
          $amount_text .= '(<strike>' . number_format($row->before_amount, 2) . '</strike>)';
        }

        if ($row->status == PAYMENT_STATUS_PENDING) {
          $amount_text = '<span class="datatable-number red" title="' . __("Pending") . '"><i>' . $amount_text . '</i></span>';
        } else {
          $amount_text = '<span class="datatable-number">' . $amount_text . '</span>';
        }

        
        
        $actions = array();
        if ($row->status == PAYMENT_STATUS_PENDING) {
          $actions[] = array(
              "label" => __("Finish"),
              "url" => 'javascript:finish_payment(' . $row->id . ')',
              "icon" => 'check'
          );
        }
        $actions[] = array(
            "label" => __("Edit"),
            "url" => base_url("payments/expenses/edit/" . $row->id),
            "icon" => 'edit'
        );
        $actions[] = array(
            "label" => __("Delete"),
            "url" => 'javascript:delete_payment(' . $row->id . ')',
            "icon" => 'trash-o'
        );
        
        $payment = array(
            "index" => ( ++$start),
            "payment_id" => $row->id,
            "registered" => my_formart_date($row->pay_date, DATE_FULL_FORMAT) . " " . $row->pay_time,
            "payment_method" => $payment_method,
            "amount" => $row->amount,
            "amount_text" => $amount_text,
            "reason" => "[" . $this->expenses_types[$row->reason_type] . "] - " . $row->reason_desc,
            "actions" => my_make_table_btn_group($actions)
        );
        $payments[] = $payment;
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'sumAllAmount' => number_format($result['amount'], 2),
        'data' => $payments
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_export_excel() {
    $this->payment_methods = $this->payment_method_m->get_methods();

    $expenses_type = $this->input->get('expenses_type');
    $company_id = $this->input->get('company_id');
    $category_id = $this->input->get('category_id');
    $payment_account = $this->input->get('payment_account');
    $payment_method = $this->input->get('payment_method');
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    $search = $this->input->get('search');

    $payments = array();
    $result = $this->payment_history_m->find(PAYMENT_TYPE_EXPENSES, $payment_account, $payment_method, $expenses_type, $search, $category_id, $start_date, $end_date, "all", false, -1, -1, $company_id);
    if ($result['count'] > 0) {
      $start = 0;
      foreach ($result['data'] as $row) {
        $payment_method = "";
        if (isset($this->payment_methods[$row->payment_method_id])) {
          $payment_method = $this->payment_methods[$row->payment_method_id];
        }

        $payment = array(
            "index" => ( ++$start),
            "registered" => $row->pay_date . " " . $row->pay_time,
            "payment_method" => $payment_method,
            "before_amount" => number_format($row->before_amount, 2),
            "amount" => number_format($row->amount, 2),
            "reason" => "[" . $this->expenses_types[$row->reason_type] . "] - " . $row->reason_desc,
            "status" => $row->status == PAYMENT_STATUS_PAID ? __("Paid") : __("Pending")
        );
        $payments[] = $payment;
      }
    }

    $this->load->library("my_export");
    $haders = array(
        __("Index"),
        __("Registed"),
        __("Payment"),
        __("First Amount(" . my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string") . ")"),
        __("Real Amount(" . my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string") . ")"),
        __("Reason"),
        __("Status")
    );
    $this->my_export->toCSV("expenses(" . $start_date . " to " . $end_date . ")", $haders, $payments);
  }

  public function ajax_find_by_products() {
    $this->payment_methods = $this->payment_method_m->get_methods();
    $company_id = $this->input->post('company_id');
    $category_id = $this->input->post('category_id');
    $payment_account = $this->input->post('payment_account');
    $payment_method = $this->input->post('payment_method');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');

    $payments = array();
    $result = $this->expenses_payment_m->find($company_id, $category_id, $payment_account, $payment_method, $start_date, $end_date, $search['value'], $order, $start, $length);
    if ($result['count'] > 0) {
      foreach ($result['data'] as $row) {
        $payment_method = "";
        if (isset($this->payment_methods[$row->payment_method_id])) {
          $payment_method = $this->payment_methods[$row->payment_method_id];
        }

        $payment = array(
            "index" => ( ++$start),
            "payment_id" => $row->id,
            "registered" => $row->pay_date . " " . $row->pay_time,
            "payment_method" => $payment_method,
            "amount" => '<span class="datatable-number">' . number_format($row->amount, 2) . '</span>',
            "company" => $row->company,
            "category" => $row->category,
            "product" => $row->product,
            "actions" => my_make_table_edit_btn(base_url("payments/expenses/edit/" . $row->id))
            . my_make_table_delete_btn('javascript:delete_payment(' . $row->id . ')')
        );
        $payments[] = $payment;
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'sumAllAmount' => number_format($result['amount'], 2),
        'data' => $payments
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_export_excel_by_products() {
    $this->payment_methods = $this->payment_method_m->get_methods();
    $company_id = $this->input->get('company_id');
    $category_id = $this->input->get('category_id');
    $payment_account = $this->input->get('payment_account');
    $payment_method = $this->input->get('payment_method');
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    $search = $this->input->get('search');

    $payments = array();
    $result = $this->expenses_payment_m->find($company_id, $category_id, $payment_account, $payment_method, $start_date, $end_date, $search, false, -1, -1);
    if ($result['count'] > 0) {
      $start = 0;
      foreach ($result['data'] as $row) {
        $payment_method = "";
        if (isset($this->payment_methods[$row->payment_method_id])) {
          $payment_method = $this->payment_methods[$row->payment_method_id];
        }

        $payment = array(
            ( ++$start),
            "registered" => $row->pay_date . " " . $row->pay_time,
            $payment_method,
            number_format($row->amount, 2),
            $row->company,
            $row->category,
            $row->product
        );
        $payments[] = $payment;
      }
    }

    $this->load->library("my_export");
    $haders = array(
        __("Index"),
        __("Registed"),
        __("Payment"),
        __("Amount(" . my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string") . ")"),
        __("Company"),
        __("Category"),
        __("Product")
    );
    $this->my_export->toCSV("expenses(" . $start_date . " to " . $end_date . ")", $haders, $payments);
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->payment_history_m->get_by_id($id);
    if ($this->payment_history_m->is_exists()) {
      if ($this->payment_history_m->reason_type == PAYMENT_REASON_TYPE_MOVEMENT) {
        $this->payment_history_m->delete_by_id($this->payment_history_m->detail_id);
      } else {
        $this->expenses_payment_m->delete_by_id($this->payment_history_m->detail_id);
      }
      $this->payment_history_m->delete_by_id($id);
    }
  }

}
