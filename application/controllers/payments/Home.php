<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Payments");
  }

  public function index() {
    $this->load->view('payments/home');
  }

}
