<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Income extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Income");

    $this->income_types = my_get_income_types();

    $this->load->model('payment_history_m');

    if ($this->uri->rsegment(2) == 'add') {
      $this->load->helper('form');
      $this->page_title = __("Add New Income");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->load->helper('form');
      $this->payment_history_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Income") . ": #" . $this->payment_history_m->id;
    }
  }

  public function index() {
    $this->payment_methods = $this->payment_method_m->get_methods(false);

    $this->load->library("my_bs_form");

    $this->my_save_current_url();

    $params = $this->uri->ruri_to_assoc();
    $input_params = array(
        "income_type" => isset($params['type']) ? $params['type'] : "all",
        "payment_account" => isset($params['account']) ? $params['account'] : "all",
        "payment_status" => isset($params['status']) ? $params['status'] : "all",
    );
    $this->load->view("payments/income/list", $input_params);
    $this->load->view('js/daterange');
  }

  public function add() {
    $step = $this->uri->rsegment(3);

    $this->payment_history_m->from_payment_method = "";
    $this->payment_history_m->to_payment_method = "";
    $this->payment_history_m->status = PAYMENT_STATUS_PAID;

    if ($step == 'second') {
      $error_msgs = array();

      $reason_type = $this->uri->rsegment(4);
      $this->payment_methods = $this->payment_method_m->get_methods($reason_type != PAYMENT_REASON_TYPE_MOVEMENT);

      if ($reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
        $this->load->model("education_payment_m");
        $this->load->model("education_class_m");
        $this->load->model("education_product_m");
        $this->load->model("education_class_student_m");

        $this->class_id = $this->uri->rsegment(5);
        $this->education_class_m->get_by_id($this->class_id);
        $this->education_product_m->get_by_id($this->education_class_m->product_id);
      } elseif ($reason_type == PAYMENT_REASON_TYPE_TANGIBLE) {
        $this->load->model("tangible_category_m");
        $this->load->model("tangible_product_m");
        $this->load->model("tangible_payment_m");
      } elseif ($reason_type == PAYMENT_REASON_TYPE_MOVEMENT) {
        $this->payment_history_m->detail_id = 0;

        $expenses_payment_history_m = new Payment_history_m();
      }

      if ($this->input->post("action") == 'process') {
        if ($reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
          $error_msgs = $this->save_education();
          if ($error_msgs === false) {
            redirect("payments/income/edit/" . $this->payment_history_m->id);
          }
        } elseif ($reason_type == PAYMENT_REASON_TYPE_TANGIBLE) {
          $error_msgs = $this->save_tangible();
          if ($error_msgs === false) {
            redirect("payments/income/edit/" . $this->payment_history_m->id);
          }
        } elseif ($reason_type == PAYMENT_REASON_TYPE_INCOME) {
          $error_msgs = $this->save_income();
          if ($error_msgs === false) {
            redirect("payments/income/edit/" . $this->payment_history_m->id);
          }
        } elseif ($reason_type == PAYMENT_REASON_TYPE_MOVEMENT) {
          $error_msgs = $this->save_movement($expenses_payment_history_m);
          if ($error_msgs === false) {
            redirect("payments/income/edit/" . $this->payment_history_m->id);
          }
        }
      }

      $this->_init_form_data($reason_type);

      $this->load->library("my_bs_form");
      $this->load->view("payments/income/edit_" . $reason_type, array("error_msgs" => $error_msgs));
    } else {
      $error_msgs = array();
      if ($this->input->post("action") == 'process') {
        $reason_type = $this->input->post("reason_type");
        if ($reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
          $class_id = $this->input->post("class_id");
          if ($class_id) {
            redirect("payments/income/add/second/" . $reason_type . "/" . $class_id);
          } else {
            $error_msgs = "Please chooise class";
          }
        } else {
          redirect("payments/income/add/second/" . $reason_type);
        }
      }

      $this->load->model("education_class_m");
      $this->classes = $this->education_class_m->get_classes(array("plan", "running"));

      $this->load->library("my_bs_form");
      $this->load->view("payments/income/select_reason", array("income_type" => $this->uri->segment(4), "error_msgs" => $error_msgs));
    }
  }

  public function edit() {
    $payment_history_id = $this->uri->segment(4);
    $this->payment_history_m->get_by_id($payment_history_id);

    $error_msgs = array();

    $reason_type = $this->payment_history_m->reason_type;

    $this->payment_methods = $this->payment_method_m->get_methods($reason_type != PAYMENT_REASON_TYPE_MOVEMENT);

    if ($reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
      $this->load->model("education_payment_m");
      $this->load->model("education_class_m");
      $this->load->model("education_product_m");
      $this->load->model("education_class_student_m");

      $this->education_payment_m->get_by_id($this->payment_history_m->detail_id);
      $this->education_class_student_m->get_by_id($this->education_payment_m->class_student_id);

      $this->class_id = $this->education_class_student_m->class_id;
      $this->education_class_m->get_by_id($this->class_id);
      $this->education_product_m->get_by_id($this->education_class_m->product_id);
    } elseif ($reason_type == PAYMENT_REASON_TYPE_TANGIBLE) {
      $this->load->model("tangible_category_m");
      $this->load->model("tangible_product_m");
      $this->load->model("tangible_payment_m");

      if ($this->payment_history_m->is_exists()) {
        $this->tangible_payment_m->get_by_id($this->payment_history_m->detail_id);
        $this->tangible_product_m->get_by_id($this->tangible_payment_m->product_id);
        $this->tangible_payment_m->category_id = $this->tangible_product_m->category_id;
      }
    } elseif ($reason_type == PAYMENT_REASON_TYPE_MOVEMENT) {
      $expenses_payment_history_m = new Payment_history_m();
      $expenses_payment_history_m->get_by_id($this->payment_history_m->detail_id);

      $this->payment_history_m->from_payment_method = $expenses_payment_history_m->payment_method_id;
      $this->payment_history_m->to_payment_method = $this->payment_history_m->payment_method_id;
    }

    if ($this->input->post("action") == 'process') {
      if ($reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
        $error_msgs = $this->save_education();
        if ($error_msgs === false) {
          redirect("payments/income/edit/" . $this->payment_history_m->id);
        }
      } elseif ($reason_type == PAYMENT_REASON_TYPE_INCOME) {
        $error_msgs = $this->save_income();
        if ($error_msgs === false) {
          redirect("payments/income/edit/" . $this->payment_history_m->id);
        }
      } elseif ($reason_type == PAYMENT_REASON_TYPE_TANGIBLE) {
        $error_msgs = $this->save_tangible();
        if ($error_msgs === false) {
          redirect("payments/income/edit/" . $this->payment_history_m->id);
        }
      } elseif ($reason_type == PAYMENT_REASON_TYPE_MOVEMENT) {
        $error_msgs = $this->save_movement($expenses_payment_history_m);
        if ($error_msgs === false) {
          redirect("payments/income/edit/" . $this->payment_history_m->id);
        }
      }
    }

    $this->_init_form_data($reason_type);

    $this->load->library("my_bs_form");

    $this->load->view("payments/income/edit_" . $reason_type, array("error_msgs" => $error_msgs, "reason"));

    $this->payment_history_m->reason = $this->income_types[$this->payment_history_m->reason_type];
    $this->payment_history_m->payment_method = $this->payment_methods[$this->payment_history_m->payment_method_id];
    $this->load->view("payments/income/receipt_modal", array("payment_m" => $this->payment_history_m));
  }

  private function _init_form_data($reason_type) {
    if ($reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
      $result = $this->education_class_student_m->find($this->class_id, "", false, false);
      $this->class_students = array();
      if ($result['count'] > 0) {
        foreach ($result['data'] as $student) {
          if ($student->status == 'hold')
            continue;
          $this->class_students[$student->id] = $student->first_name . " " . $student->last_name;
        }
      }

      $current_unit = my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string");
      $this->class_steps = array();
      $this->class_steps_cost = array();
      if ($this->education_class_m->registration_fee * 1 != 0) {
        $this->class_steps[0] = __("Fee") . " : " . $current_unit . $this->education_class_m->registration_fee;
        $this->class_steps_cost[0] = $this->education_class_m->registration_fee;
      }

      $this->load->model("education_class_step_m");
      $class_steps = $this->education_class_step_m->get_by_class_id($this->class_id);
      foreach ($class_steps as $step) {
        $this->class_steps[$step->id] = __("Step") . $step->step . " - " . my_formart_date($step->due_date, DATE_FULL_FORMAT) . " : " . $current_unit . $step->cost;
        $this->class_steps_cost[$step->id] = $step->cost;
      }
    } elseif ($reason_type == PAYMENT_REASON_TYPE_TANGIBLE) {
      $this->categories = $this->tangible_category_m->get_categories();

      $this->products = array();
      if ($this->tangible_payment_m->category_id) {
        $this->products = $this->tangible_product_m->get_products($this->tangible_payment_m->category_id);
      } else {
        if (!empty($this->categories)) {
          $cat_ids = array_keys($this->categories);
          $this->products = $this->tangible_product_m->get_products($cat_ids[0]);
        }
      }
    }
  }

  private function save_income() {
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
            'field' => 'payment_method_id',
            'label' => __('Method'),
            'rules' => 'required'
        ),
        array(
            'field' => 'before_amount',
            'label' => __('Amount'),
            'rules' => ('required|greater_than[0]'),
        ),
        array(
            'field' => 'amount',
            'label' => __('Amount'),
            'rules' => ('required|greater_than[0]'),
        ),
        array(
            'field' => 'reason',
            'label' => __('Reason'),
            'rules' => ('required'),
        )
    );

    $this->form_validation->set_rules($valid_config);

    $this->payment_history_m->type = PAYMENT_TYPE_INCOME;

    $this->payment_history_m->payment_method_id = $this->input->post("payment_method_id");
    $this->payment_history_m->pay_date = my_formart_date($this->input->post("pay_date"), 'Y-m-d');
    $this->payment_history_m->pay_time = $this->input->post("pay_time_h") . ":" . $this->input->post("pay_time_m");
    $this->payment_history_m->status = $this->input->post("payment_status");

    $this->payment_history_m->before_amount = $this->input->post('before_amount');
    $this->payment_history_m->amount = $this->input->post('amount');
    $this->payment_history_m->paid_amount = $this->payment_history_m->amount;
    $this->payment_history_m->discount_type = $this->input->post('discount_type');
    if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_NO) {
      $this->payment_history_m->discount = 0;
    } else {
      $this->payment_history_m->discount = $this->input->post('discount');
    }

    $this->payment_history_m->reason_type = PAYMENT_REASON_TYPE_INCOME;
    $this->payment_history_m->reason_desc = $this->input->post("reason");

    if ($this->form_validation->run() == FALSE) {
      $error_msgs = $this->form_validation->error_array();
    } else {
      $this->payment_history_m->detail_id = 0;

      if ($this->payment_history_m->save()) {
        my_set_system_message(__("Successfully save payment."), "success");
      } else {
        $error_msgs = array(__("Failed save payment."));
      }
    }

    return $error_msgs;
  }

  private function save_education() {
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
            'field' => 'payment_method_id',
            'label' => __('Method'),
            'rules' => 'required'
        ),
        array(
            'field' => 'student_id',
            'label' => __('Student'),
            'rules' => 'required'
        ),
        array(
            'field' => 'class_step',
            'label' => __('Step'),
            'rules' => 'required'
        ),
        array(
            'field' => 'before_amount',
            'label' => __('Amount'),
            'rules' => ('required|greater_than[0]'),
        ),
        array(
            'field' => 'amount',
            'label' => __('Amount'),
            'rules' => ('required|greater_than[0]'),
        )
    );

    $this->form_validation->set_rules($valid_config);

    $this->education_payment_m->class_id = $this->class_id;
    $this->education_payment_m->class_student_id = $this->input->post('student_id');
    $this->education_payment_m->paid_step_id = $this->input->post('class_step');
    $this->education_payment_m->paid_amount = $this->input->post('amount');
    $this->education_payment_m->paid_date = my_formart_date($this->input->post('pay_date'), 'Y-m-d');

    $this->payment_history_m->type = PAYMENT_TYPE_INCOME;
    $this->payment_history_m->payment_method_id = $this->input->post("payment_method_id");
    $this->payment_history_m->pay_date = $this->education_payment_m->paid_date;
    $this->payment_history_m->pay_time = $this->input->post("pay_time_h") . ":" . $this->input->post("pay_time_m");
    $this->payment_history_m->status = $this->input->post("payment_status");

    $this->payment_history_m->before_amount = $this->input->post('before_amount');
    $this->payment_history_m->amount = $this->input->post('amount');
    $this->payment_history_m->paid_amount = $this->input->post('paid_amount');
    $this->payment_history_m->discount_type = $this->input->post('discount_type');
    if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_NO) {
      $this->payment_history_m->discount = 0;
    } else {
      $this->payment_history_m->discount = $this->input->post('discount');
    }

    if ($this->form_validation->run() == FALSE) {
      $error_msgs = $this->form_validation->error_array();
    } else {
      $this->education_payment_m->save();

      $this->payment_history_m->reason_type = PAYMENT_REASON_TYPE_EDUCATION;

      $this->load->model("student_m");
      $this->education_class_student_m->get_by_id($this->education_payment_m->class_student_id);
      $this->student_m->get_by_id($this->education_class_student_m->student_id);

      $student_name = $this->student_m->get_name();
      
      $step = "";
      if ($this->education_payment_m->paid_step_id == 0) {
        $step = __("fee");
      } else {
        $this->load->model("education_class_step_m");
        $this->education_class_step_m->get_by_id($this->education_payment_m->paid_step_id);
        
        $step = __("step") . $this->education_class_step_m->step;
      }
      
      $class_name = $this->education_product_m->name;
      $this->payment_history_m->reason_desc = __("%s paid for %s of %s", $step, $student_name, $class_name);
      $this->payment_history_m->detail_id = $this->education_payment_m->id;

      if ($this->payment_history_m->save()) {
        my_set_system_message(__("Successfully save payment."), "success");
      } else {
        $error_msgs = array(__("Failed save payment."));
      }
    }

    return $error_msgs;
  }

  private function save_tangible() {
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
            'field' => 'payment_method_id',
            'label' => __('Method'),
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

    $this->tangible_payment_m->category_id = $this->input->post('category_id');
    $this->tangible_payment_m->product_id = $this->input->post('product_id');

    $this->payment_history_m->type = PAYMENT_TYPE_INCOME;
    $this->payment_history_m->payment_method_id = $this->input->post("payment_method_id");
    $this->payment_history_m->pay_date = my_formart_date($this->input->post("pay_date"), 'Y-m-d');
    $this->payment_history_m->pay_time = $this->input->post("pay_time_h") . ":" . $this->input->post("pay_time_m");
    $this->payment_history_m->status = $this->input->post("payment_status");

    $this->payment_history_m->before_amount = $this->input->post('before_amount');
    $this->payment_history_m->amount = $this->input->post('amount');
    $this->payment_history_m->paid_amount = $this->input->post('paid_amount');
    $this->payment_history_m->discount_type = $this->input->post('discount_type');
    if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_NO) {
      $this->payment_history_m->discount = 0;
    } else {
      $this->payment_history_m->discount = $this->input->post('discount');
    }

    if ($this->form_validation->run() == FALSE) {
      $error_msgs = $this->form_validation->error_array();
    } else {
      $this->tangible_payment_m->save();

      $this->payment_history_m->reason_type = PAYMENT_REASON_TYPE_TANGIBLE;
      if ($this->tangible_payment_m->product_id > 0) {
        $this->tangible_product_m->get_by_id($this->tangible_payment_m->product_id);
        $this->payment_history_m->reason_desc = $this->tangible_product_m->name;
      }

      $this->payment_history_m->detail_id = $this->tangible_payment_m->id;

      if ($this->payment_history_m->save()) {
        my_set_system_message(__("Successfully save payment."), "success");
      } else {
        $error_msgs = array(__("Failed save payment."));
      }
    }

    return $error_msgs;
  }

  public function ajax_find() {
    $this->payment_methods = $this->payment_method_m->get_methods();

    $payment_status = $this->input->post('payment_status');
    $income_type = $this->input->post('income_type');
    if ($income_type == PAYMENT_REASON_TYPE_EDUCATION) {
      $category_id = $this->input->post('class_id');
    } else {
      $category_id = $this->input->post('category_id');
    }
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
    $result = $this->payment_history_m->find(PAYMENT_TYPE_INCOME, $payment_account, $payment_method, $income_type, $search['value'], $category_id, $start_date, $end_date, $payment_status, $order, $start, $length);
    if ($result['count'] > 0) {
      foreach ($result['data'] as $row) {
        $payment_method = "";
        if (isset($this->payment_methods[$row->payment_method_id])) {
          $payment_method = $this->payment_methods[$row->payment_method_id];
        }

        $amount_text = number_format($row->amount, 2);
        if ($row->amount != $row->before_amount) {
          $amount_text .= '(<strike><i>' . number_format($row->before_amount, 2) . '</i></strike>)';
        }

        $actions = array();
        $actions[] = array(
            "label" => __("Receipt"),
            "url" => 'javascript:show_receipt(' . $row->id . ')',
            "icon" => 'list-alt'
        );
        if ($row->status == PAYMENT_STATUS_PENDING) {
          $actions[] = array(
              "label" => __("Finish"),
              "url" => 'javascript:finish_payment(' . $row->id . ')',
              "icon" => 'check'
          );
        }
        $actions[] = array(
            "label" => __("Edit"),
            "url" => base_url("payments/income/edit/" . $row->id),
            "icon" => 'edit'
        );
        $actions[] = array(
            "label" => __("Delete"),
            "url" => 'javascript:delete_payment(' . $row->id . ')',
            "icon" => 'trash-o'
        );

        $payment = array(
            "index" => ( ++$start),
            "registered" => my_formart_date($row->pay_date, DATE_FULL_FORMAT) . " " . $row->pay_time,
            "payment_method" => $payment_method,
            "amount_text" => $amount_text,
            "amount" => $row->amount,
            "paid_amount" => $row->paid_amount,
            "change" => $row->paid_amount - $row->amount,
            "reason" => $this->income_types[$row->reason_type] . " / " . $row->reason_desc,
            "status" => $row->status == PAYMENT_STATUS_PENDING ? '<button type="button" class="btn btn-xs red">' . __("Pending") . '</button>' : '<button type="button" class="btn btn-xs blue">' . __("Paid") . '</button>',
            "actions" => my_make_table_btn_group($actions),
            "payment_id" => $row->id,
            "before_amount" => $row->before_amount,
            "discount" => ($row->discount_type == 'percent' ? $row->discount . "%" : "$" . $row->discount),
            "discount_amount" => ($row->before_amount - $row->amount),
            "is_enable_email" => $row->reason_type == PAYMENT_REASON_TYPE_EDUCATION || $row->reason_type == PAYMENT_REASON_TYPE_TANGIBLE
        );
        $payments[] = $payment;
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'sumAllAmount' => $result['amount'],
        'sumAllPaid' => $result['paid'],
        'data' => $payments
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_export_excel() {
    $this->payment_methods = $this->payment_method_m->get_methods();

    $payment_status = $this->input->get('payment_status');
    $income_type = $this->input->get('income_type');
    if ($income_type == PAYMENT_REASON_TYPE_EDUCATION) {
      $category_id = $this->input->post('class_id');
    } else {
      $category_id = $this->input->post('category_id');
    }
    $payment_account = $this->input->get('payment_account');
    $payment_method = $this->input->get('payment_method');
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    $search = $this->input->get('search');

    $payments = array();
    $result = $this->payment_history_m->find(PAYMENT_TYPE_INCOME, $payment_account, $payment_method, $income_type, $search, $category_id, $start_date, $end_date, $payment_status, false, -1, -1);

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
            "reason" => "[" . $this->income_types[$row->reason_type] . "] - " . $row->reason_desc,
            "before_amount" => number_format($row->before_amount, 2),
            "amount" => number_format($row->amount, 2),
            "payment_method" => $payment_method,
            "paid_amount" => number_format($row->paid_amount, 2),
            "change_amount" => ($row->paid_amount == $row->amount ? "" : number_format($row->paid_amount - $row->amount, 2)),
            "status" => $row->status == PAYMENT_STATUS_PAID ? __("Paid") : __("Pending")
        );
        $payments[] = $payment;
      }
    }

    $this->load->library("my_export");
    $haders = array(
        __("Index"),
        __("Registed"),
        __("Reason"),
        __("First Amount(" . my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string") . ")"),
        __("Real Amount(" . my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string") . ")"),
        __("Payment"),
        __("Paid Amount(" . my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string") . ")"),
        __("Change Amount(" . my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string") . ")"),
        __("Status")
    );
    $this->my_export->toCSV("income(" . $start_date . " to " . $end_date . ")", $haders, $payments);
  }

  public function ajax_finish() {
    $id = $this->uri->rsegment(3);
    $this->payment_history_m->get_by_id($id);
    if ($this->payment_history_m->is_exists()) {
      $this->payment_history_m->status = PAYMENT_STATUS_PAID;
      $this->payment_history_m->save();
    }
  }

  private function save_movement($expenses_payment_history_m) {
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
            'field' => 'amount',
            'label' => __('Amount'),
            'rules' => ('required|greater_than[0]'),
        )
    );

    $this->form_validation->set_rules($valid_config);

    $this->payment_history_m->to_payment_method = $this->input->post("to_payment_method");
    $this->payment_history_m->from_payment_method = $this->input->post("from_payment_method");

    $this->payment_history_m->type = PAYMENT_TYPE_INCOME;
    $this->payment_history_m->payment_method_id = $this->payment_history_m->to_payment_method;
    $this->payment_history_m->pay_date = my_formart_date($this->input->post("pay_date"), 'Y-m-d');
    $this->payment_history_m->pay_time = $this->input->post("pay_time_h") . ":" . $this->input->post("pay_time_m");
    $this->payment_history_m->before_amount = $this->payment_history_m->amount = $this->payment_history_m->paid_amount = $this->input->post('amount');
    $this->payment_history_m->discount_type = DISCOUNT_TYPE_NO;
    $this->payment_history_m->discount = 0;
    $this->payment_history_m->reason_type = PAYMENT_REASON_TYPE_MOVEMENT;
    $this->payment_history_m->reason_desc = __("Movement from Caja Chica to Bank");
    $this->payment_history_m->status = PAYMENT_STATUS_PAID;

    if ($this->form_validation->run() == FALSE) {
      $error_msgs = $this->form_validation->error_array();
    } else {
      if ($this->payment_history_m->save()) {
        $expenses_payment_history_m->type = PAYMENT_TYPE_EXPENSES;
        $expenses_payment_history_m->payment_method_id = $this->payment_history_m->from_payment_method;
        $expenses_payment_history_m->pay_date = $this->payment_history_m->pay_date;
        $expenses_payment_history_m->pay_time = $this->input->post("pay_time_h") . ":" . $this->input->post("pay_time_m");
        $expenses_payment_history_m->before_amount = $expenses_payment_history_m->amount = $this->payment_history_m->paid_amount = $this->input->post('amount');
        $expenses_payment_history_m->discount_type = DISCOUNT_TYPE_NO;
        $expenses_payment_history_m->discount = 0;
        $expenses_payment_history_m->reason_type = PAYMENT_REASON_TYPE_MOVEMENT;
        $expenses_payment_history_m->reason_desc = __("Movement from Caja Chica to Bank");
        $expenses_payment_history_m->status = PAYMENT_STATUS_PAID;
        $expenses_payment_history_m->detail_id = $this->payment_history_m->id;
        $expenses_payment_history_m->save();

        $this->payment_history_m->detail_id = $expenses_payment_history_m->id;
        $this->payment_history_m->save();

        my_set_system_message(__("Successfully save payment."), "success");
      } else {
        $expenses_payment_history_m->delete();
        $error_msgs = array(__("Failed save payment."));
      }
    }

    return $error_msgs;
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->payment_history_m->get_by_id($id);
    if ($this->payment_history_m->is_exists()) {
      if ($this->payment_history_m->reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
        $this->load->model("education_payment_m");
        $this->education_payment_m->delete_by_id($this->payment_history_m->detail_id);
      } elseif ($this->payment_history_m->reason_type == PAYMENT_REASON_TYPE_TANGIBLE) {
        $this->load->model("tangible_payment_m");
        $this->tangible_payment_m->delete_by_id($this->payment_history_m->detail_id);
      } elseif ($this->payment_history_m->reason_type == PAYMENT_REASON_TYPE_MOVEMENT) {
        $this->payment_history_m->delete_by_id($this->payment_history_m->detail_id);
      }
      $this->payment_history_m->delete_by_id($id);
    }
  }

  public function ajax_receipt_pdf() {
    $id = $this->uri->rsegment(3);
    $this->payment_history_m->get_by_id($id);

    $this->payment_methods = $this->payment_method_m->get_methods();

//    $this->load->view("payments/receipt/pdf");

    $this->load->library("pdf");

    $this->pdf->load_view('payments/receipt/pdf', array("payment_history_m" => $this->payment_history_m));
    $this->pdf->render();
    $this->pdf->stream("Receipt#" . $id . "(" . $this->payment_history_m->pay_date . "_" . $this->payment_history_m->pay_time . ").pdf");

    exit;
  }

  public function ajax_get_receiver() {
    $id = $this->uri->rsegment(3);
    $this->payment_history_m->get_by_id($id);

    $receiver = array(
        "name" => "",
        "email" => ""
    );
    if ($this->payment_history_m->reason_type == PAYMENT_REASON_TYPE_EDUCATION) {
      $this->load->model("education_payment_m");
      $this->education_payment_m->get_by_id($this->payment_history_m->detail_id);

      $this->load->model("education_class_student_m");
      $this->education_class_student_m->get_by_id($this->education_payment_m->class_student_id);

      $this->load->model("student_m");
      $this->student_m->get_by_id($this->education_class_student_m->student_id);

      $receiver['name'] = $this->student_m->get_name();
      if ($this->student_m->invoice_email) {
        $receiver['email'] = $this->student_m->invoice_email;
      } else {
        $receiver['email'] = $this->student_m->email;
      }
    }

    header('Content-Type: application/json');
    echo json_encode($receiver);
  }

  public function ajax_receipt_email() {
    $payment_id = $this->input->post("payment_id");
    $receiver_email = $this->input->post("receiver_email");
    
    $this->payment_history_m->get_by_id($payment_id);

    $this->payment_methods = $this->payment_method_m->get_methods();

    //$this->load->view("emails/receipt_temp");

    $message = $this->load->view("emails/receipt_temp", '', true);

    $this->config->load('email');
    $config = $this->config->item('email');
    
    $this->load->library('email', $config);
    $this->email->from(CONTACT_EMAIL, SITE_TITLE);
    $this->email->to($receiver_email);

    $this->email->subject(__("Thank you for your trust"));
    $this->email->message($message);

    if ($this->email->send()) {
      echo 'OK';
    } else {
      echo 'NO';
    }
  }

}
