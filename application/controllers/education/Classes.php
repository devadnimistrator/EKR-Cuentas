<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->model("education_class_m");

    $this->page_title = __("Classes");
    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Class");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->education_class_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Class") . ": #" . $this->education_class_m->id;
    }
  }

  public function index() {
    $this->load->view('js/daterange');
    $this->load->view("education/classes/list");
  }

  public function add() {
    $this->load->model("education_category_m");

    $this->education_category_m->category_id = 0;

    if ($this->input->post('action') == 'process') {
      if ($this->education_class_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        $this->education_class_m->start_datetime = my_formart_date($this->education_class_m->start_datetime, 'Y-m-d H:i');
        $this->education_class_m->end_datetime = '';
        $this->education_class_m->cancel_date = '';
        $this->education_class_m->status = 'plan';
        $this->education_class_m->save();

        $new_class_id = $this->education_class_m->id;

        $cost = $this->input->post("cost");
        $due_date = $this->input->post("due_date");
        $this->load->model("education_class_step_m");

        $step = 0;
        for ($i = 0; $i < count($cost); $i ++) {
          if ($cost[$i] && $due_date[$i]) {
            $step ++;
            $this->education_class_step_m->init_values();
            $this->education_class_step_m->class_id = $new_class_id;
            $this->education_class_step_m->cost = $cost[$i];
            $this->education_class_step_m->step = $step;
            $this->education_class_step_m->due_date = my_formart_date($due_date[$i], 'Y-m-d');
            $this->education_class_step_m->save();
          }
        }

        my_set_system_message(__("Successfully added new class."), "success");

        redirect("education/classes/edit/" . $new_class_id);
      }
    }

    $this->categories = $this->education_category_m->get_all_categories(false);

    $view_params = array();

    $this->load->view("education/classes/add", $view_params);
  }

  public function edit() {
    $this->load->model("education_category_m");
    $this->load->model("education_class_step_m");

    if ($this->input->post('action') == 'process') {
      $valied = $this->education_class_m->form_validate($this->input->post());
      if ($this->education_class_m->status == 'cancel') {
        if ($this->education_class_m->cancel_date == '') {
          $this->education_class_m->add_error("cancel_date", __("Please input cancel date."));
          $valied = FALSE;
        }
      } else {
        $this->education_class_m->cancel_date = '';

        if ($this->education_class_m->status == 'end') {
          if ($this->education_class_m->end_datetime == '') {
            $this->education_class_m->add_error("end_datetime", __("Please input end datetime."));
            $valied = FALSE;
          }
        }
      }

      if ($valied == FALSE) {
        
      } else {
        $this->education_class_m->start_datetime = my_formart_date($this->education_class_m->start_datetime, 'Y-m-d H:i');
        $this->education_class_m->save();

        $class_id = $this->education_class_m->id;

        $step_id = $this->input->post("step_id");
        $cost = $this->input->post("cost");
        $due_date = $this->input->post("due_date");

        for ($step = 0; $step < count($step_id); $step ++) {
          $this->education_class_step_m->init_values();
          $this->education_class_step_m->id = $step_id[$step];
          if ($cost[$step] && $due_date[$step]) {
            $this->education_class_step_m->class_id = $class_id;
            $this->education_class_step_m->step = $step + 1;
            $this->education_class_step_m->cost = $cost[$step];
            $this->education_class_step_m->due_date = my_formart_date($due_date[$step], 'Y-m-d');
            $this->education_class_step_m->save();
          } else {
            $this->db->query("delete from education_payments where paid_step_id='" . $step_id[$step] . "'");
            $this->education_class_step_m->delete();
          }
        }

        my_set_system_message(__("Successfully updated class."), "success");

        redirect("education/classes/edit/" . $class_id);
      }
    }


    $this->load->model("education_product_m");
    $this->education_product_m->get_by_id($this->education_class_m->product_id);
    $this->education_class_m->category_id = $this->education_product_m->category_id;
    $this->categories = $this->education_category_m->get_all_categories(false);

    $view_params = array();

    $this->load->view("education/classes/edit", $view_params);
  }

  public function ajax_find() {
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');

    $start_date = $this->input->post("start_date");
    $end_date = $this->input->post("end_date");
    $status = $this->input->post("status");
    $category_id = $this->input->post("category_id");

    $result = $this->education_class_m->find($start_date, $end_date, $status, $category_id, $search['value'], $order, $start, $length);
    $classes = array();
    if ($result['count'] > 0) {
      $this->load->model("education_class_step_m");
      $this->load->model("education_class_student_m");

      foreach ($result['data'] as $class) {
        $status = my_formart_date($class->start_datetime, DATETIME1_FULL_FORMAT);
        $status .= '<br/>';
        if ($class->status == 'plan') {
          $status .= '<i class="fa fa-circle green"></i> <small>plan</small>';
        } elseif ($class->status == 'running') {
          $status .= '<i class="fa fa-circle blue"></i> <small>running</small>';
        } elseif ($class->status == 'end') {
          $status .= '<i class="fa fa-circle dark"></i> <small>ended in ' . my_formart_date($class->end_datetime, DATETIME1_FULL_FORMAT) . '</small>';
        } elseif ($class->status == 'cancel') {
          $status .= '<i class="fa fa-circle red"></i> <small>cancel in ' . my_formart_date($class->cancel_date, DATETIME1_FULL_FORMAT) . '</small>';
        }
        $description = $class->product_name;
        $description.= "<br/>";
        $description.= __("Fee") . ": " . my_get_currency_unit() . $class->registration_fee;

        $class_steps = $this->education_class_step_m->count_by_class_id($class->id);
        $description.= " / " . __("Steps: ") . $class_steps;

        $actions = array();
        $actions[] = array(
            "label" => __("Edit"),
            "url" => base_url("education/classes/edit/" . $class->id),
            "icon" => 'edit'
        );
        $actions[] = array(
            "label" => __("Delete"),
            "url" => 'javascript:delete_class(' . $class->id . ')',
            "icon" => 'trash-o'
        );

        $students = $this->education_class_student_m->count_by_class_id($class->id);
        $description.= " / " . __("Students: ") . $students;
        $classes[] = array(
            "index" => ( ++$start),
            "class_id" => $class->id,
            "start_datetime" => $status,
            "description" => $description,
            "actions" => my_make_table_btn_group($actions)
        );
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'data' => $classes
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_delete_class() {
    $this->education_class_m->get_by_id($this->uri->rsegment(3));
    $this->education_class_m->delete();
  }

  public function ajax_add_student() {
    $this->load->model("education_class_student_m");

    $this->db->where("class_id", $this->input->post('class_id'));
    $this->education_class_student_m->get_by_student_id($this->input->post('student_id'));
    if ($this->education_class_student_m->is_exists()) {
      echo 'error';
    } else {
      $this->education_class_student_m->class_id = $this->input->post('class_id');
      $this->education_class_student_m->student_id = $this->input->post('student_id');
      $this->education_class_student_m->status = "joined";
      $this->education_class_student_m->save();

      echo 'ok';
    }

    exit;
  }

  public function ajax_get_students() {
    $class_id = $this->uri->segment(4);

    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $search = $this->input->post('search');

    $this->load->model("education_class_student_m");
    $result = $this->education_class_student_m->find($class_id, $search['value'], $start, $length);
    $students = array();
    if ($result['count'] > 0) {

      foreach ($result['data'] as $student) {
        $this->db->select_sum('paid_amount');
        $query = $this->db->where('class_student_id', $student->id)->get('education_payments')->row();

        $staus = "";
        $actions = my_make_table_delete_btn("javascript:deleteStudent(" . $student->id . ")");
        if ($student->status == 'hold') {
          $staus = '<div class="red">' . __("Hold") . '</div>';
          $actions.= my_make_table_btn("javascript:unholdStudent(" . $student->id . ")", __("UnHold"), "info", "check");
        } else {
          $actions.= my_make_table_btn("javascript:holdStudent(" . $student->id . ")", __("Hold"), "danger", "stop");
        }


        $students[] = array(
            "index" => ( ++$start),
            "student_id" => $student->id,
            "name" => $student->first_name . " " . $student->last_name,
            "amount" => '<a class="datatable-number" href="javascript:show_pay_histories(' . $student->id . ')">' . number_format($query->paid_amount * 1, 2) . '</a>',
            "status" => $staus,
            "actions" => $actions
        );
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'data' => $students
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_delete_student() {
    $this->load->model("education_class_student_m");

    $this->education_class_student_m->get_by_id($this->input->post('student_id'));
    $this->education_class_student_m->delete();

    exit;
  }

  public function ajax_hold_student() {
    $this->load->model("education_class_student_m");

    $this->education_class_student_m->get_by_id($this->input->post('student_id'));
    $this->education_class_student_m->status = 'hold';
    $this->education_class_student_m->save();

    exit;
  }

  public function ajax_unhold_student() {
    $this->load->model("education_class_student_m");

    $this->education_class_student_m->get_by_id($this->input->post('student_id'));
    $this->education_class_student_m->status = 'joined';
    $this->education_class_student_m->save();

    exit;
  }

  public function ajax_get_payment_histories() {
    $draw = $this->input->post('draw');
    $class_id = $this->input->post("class_id");
    $class_student_id = $this->input->post("class_student_id");

    $query = "select * from education_payments where class_student_id='" . $class_student_id . "' and class_id='" . $class_id . "' order by paid_date";
    $result = $this->db->query($query)->result();

    $histories = array();
    $index = 0;
    foreach ($result as $row) {
      $index ++;
      $histories[] = array(
          "index" => $index,
          "date" => $row->paid_date,
          "amount" => number_format($row->paid_amount, 2)
      );
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => count($histories),
        'recordsFiltered' => count($histories),
        'data' => $histories
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_delete_step() {
    $step_id = $this->input->post('step_id');

    $this->db->query("delete from education_class_steps where `id`='" . $step_id . "'");
    $this->db->query("delete from education_payments where paid_step_id='" . $step_id . "'");
    
    sync_education_payments();
    
    die('ok');
  }

}
