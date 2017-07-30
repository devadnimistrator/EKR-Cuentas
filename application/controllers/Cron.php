<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ajax
 *
 * @author psp
 */
class Cron extends My_Controller {

  var $email_config = array();

  public function __construct() {
    parent::__construct();

    $this->config->load('email');
    $this->email_config = $this->config->item('email');
  }

  public function reminder() {
    $this->load->model("student_m");
    $this->load->model("education_product_m");
    $this->load->model("education_category_m");

    $after_15_date = my_add_date(REMINDER_DATE, false, "Y-m-d");

    $query = "select * from education_classes where registration_fee > 0 and (`status`='plan' or `status`='running') and SUBSTR(start_datetime, 1, 10) = '" . $after_15_date . "'";
    $classes = $this->db->query($query)->result();
    foreach ($classes as $class) {
      $this->education_product_m->get_by_id($class->product_id);
      $this->education_category_m->get_by_id($this->education_product_m->category_id);

      $query = "select distinct * from education_class_students where";
      $query .= " `id` not in (select class_student_id from education_payments where paid_step_id=0 and class_id='" . $class->id . "')";
      $query .= " and `id` not in (select class_student_id from education_reminder_emails where step_id=0 and `status`='1')";
      $query .= " and `class_id`='" . $class->id . "'";

      $students = $this->db->query($query)->result();
      foreach ($students as $student) {
        $this->student_m->get_by_id($student->student_id);

        $student_email = $this->student_m->invoice_email ? $this->student_m->invoice_email : $this->student_m->email;
        $student_name = $this->student_m->get_name();
//        $class_name = $this->education_category_m->name . " / " . $this->education_product_m->name;
        $class_name = $this->education_product_m->name;
        $due_date = my_formart_date($class->start_datetime, "j") . " de " . my_formart_date($class->start_datetime, "F") . " del " . my_formart_date($class->start_datetime, "Y");
        $total_due = "$" . number_format($class->registration_fee, 2);
        $payment_type = "inscripción";

        $sent_status = $this->send_reminder_email($student_email, $student_name, $class_name, $due_date, $total_due, $payment_type);

        $this->log_reminder_email($student->id, 0, $sent_status);

        echo "Sent reminder email: " . $class_name . " > " . $student_name . " : " . $sent_status;
        echo "\n";
      }
    }

    $query = "select c.product_id, s.* from education_classes c, education_class_steps s where ";
    $query .= " c.`id` = s.class_id";
    $query .= " and (c.`status`='plan' or c.`status`='running')";
    $query .= " and s.due_date = '" . $after_15_date . "'";
    $class_steps = $this->db->query($query)->result();

    foreach ($class_steps as $class_step) {
      $this->education_product_m->get_by_id($class_step->product_id);
      //$this->education_category_m->get_by_id($this->education_product_m->category_id);

      $query = "select distinct * from education_class_students where";
      $query .= " `id` not in (select class_student_id from education_payments where paid_step_id='" . $class_step->id . "' and class_id='" . $class_step->class_id . "')";
      $query .= " and `id` not in (select class_student_id from education_reminder_emails where step_id='" . $class_step->id . "' and `status`='1')";
      $query .= " and `class_id`='" . $class_step->class_id . "'";

      $students = $this->db->query($query)->result();

      foreach ($students as $student) {
        $this->student_m->get_by_id($student->student_id);

        $student_email = $this->student_m->invoice_email ? $this->student_m->invoice_email : $this->student_m->email;
        $student_name = $this->student_m->get_name();
//        $class_name = $this->education_category_m->name . " / " . $this->education_product_m->name;
        $class_name = $this->education_product_m->name;
        $due_date = my_formart_date($class_step->due_date, "j") . " de " . my_formart_date($class_step->due_date, "F") . " del " . my_formart_date($class_step->due_date, "Y");
        $total_due = "$" . number_format($class_step->cost, 2);
        $payment_type = "mensualidad " . $class_step->step;

        $sent_status = $this->send_reminder_email($student_email, $student_name, $class_name, $due_date, $total_due, $payment_type);

        $this->log_reminder_email($student->id, $class_step->id, $sent_status);

        echo "Sent reminder email: " . $class_name . " > " . $student_name . " : " . $sent_status;
        echo "\n";
      }
    }
  }

  private function send_reminder_email($student_email, $student_name, $class_name, $due_date, $total_due, $payment_type) {
    $params = array("student_name" => $student_name, "class_name" => $class_name, "due_date" => $due_date, "total_due" => $total_due, "payment_type" => $payment_type);
    $message = $this->load->view("emails/reminder_temp", $params, true);

    $this->load->library('email', $this->email_config);
    $this->email->from(CONTACT_EMAIL, SITE_TITLE);
    $this->email->to($student_email);

    $this->email->subject("Recordatorio de pago para " . $student_name);
    $this->email->message($message);

    if ($this->email->send()) {
      return true;
    } else {
      return false;
    }
  }

  private function log_reminder_email($class_student_id, $step_id, $status) {
    $log = array(
        "class_student_id" => $class_student_id,
        "step_id" => $step_id,
        "sent_date" => date('Y-m-d H:i:s'),
        "status" => $status ? 1 : 0
    );
    $this->db->insert("education_reminder_emails", $log);
  }

}
