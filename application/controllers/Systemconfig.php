<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for set system config
 *
 */
class Systemconfig extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->check_admin();

    if ($this->uri->rsegment(2) == 'sitesetting') {
      $this->page_title = __("Site Settings");
    } elseif ($this->uri->rsegment(2) == 'paymentmethods') {
      $this->page_title = __("Payment Methods");
    }
  }

  public function sitesetting() {
    if ($this->input->post('action') == 'process') {
      $configs = array(
          'SITE_TITLE' => $this->input->post('site_title'),
          'CONTACT_EMAIL' => $this->input->post('contact_email'),
          'CONTACT_PHONE' => $this->input->post('contact_phone'),
          'CONTACT_STREET' => $this->input->post('contact_street'),
          'DISPLAY_LANGUAGE' => $this->input->post('display_language'),
          'DEFAULT_COUNTRY' => $this->input->post('default_country'),
          'DEFAULT_CURRENCY_UNIT' => $this->input->post('default_currency'),
          'RECEIPT_HEADER' => $this->input->post('receipt_header'),
          'RECEIPT_FOOTER1' => $this->input->post('receipt_footer1'),
          'RECEIPT_FOOTER2' => $this->input->post('receipt_footer2'),
          'REMINDER_DATE' => $this->input->post('reminder_date')
      );

      $this->config_m->set_config($configs);

      my_set_system_message(__("Changed system configurations."), "success");

      redirect('systemconfig/sitesetting');
    }

    $this->load->view('systemconfig/sitesetting');
  }

  public function paymentmethods() {
    $params = array(
        "payment_method_m" => $this->payment_method_m
    );

    $this->load->view("systemconfig/paymentmethods", $params);
  }

  public function ajax_get_all_methods() {
    $methods = $this->payment_method_m->get_methods(false);
    $nodes = array();
    if (!empty($methods)) {
      $payment_methods = my_get_payment_methods();

      foreach ($methods as $method) {
        $nodes[] = array(
            "id" => $method->id,
            "text" => $method->name,
            "type" => $method->type,
            "tags" => array(
                $payment_methods[$method->type]
            )
        );
      }
    }

    header('Content-Type: application/json');
    echo json_encode($nodes);
  }

  public function ajax_save_method() {
    if ($this->input->post('action') == 'process') {
      if ($this->payment_method_m->form_validate($this->input->post()) == FALSE) {
        $this->payment_method_m->show_errors();
      } else {
        $this->payment_method_m->id = $this->input->post('method_id');
        $this->payment_method_m->name = $this->input->post('name');
        $this->payment_method_m->type = $this->input->post('type');
        $this->payment_method_m->save();

        if ($this->input->post('method_id')) {
          my_show_msg(__("Successfully updated method."), "success");
        } else {
          my_show_msg(__("Successfully added new method."), "success");
        }
      }
    }
  }

  public function ajax_delete_method() {
    $this->payment_method_m->get_by_id($this->uri->rsegment(3));
    if ($this->payment_method_m->is_exists()) {
      $this->payment_method_m->delete();
      my_show_msg(__("Successfully deleted method."), "success");
    }
  }

}
