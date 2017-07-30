<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Education_class_step_m extends My_Model {

  public function delete() {
    parent::delete();

    sync_education_payments();
  }

}
