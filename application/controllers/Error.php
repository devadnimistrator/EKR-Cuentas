<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for authontication
 *
 * - Signin
 * - Sugnout
 */
class Error extends CI_Controller {
  function __construct() {
    parent::__construct();
    
    my_set_default_configs();
  }
  
  function index() {
    $this->load->view("errors/html/error");
  }

  function error_404() {
    $this->load->view("errors/html/error_404");
  }

}
