<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-8 col-sm-12 col-xs-12">
    <?php $this->load->view("widgets/calendar"); ?>
  </div>
  
  <div class="col-md-4 col-sm-12 col-xs-12">
    <?php $this->load->view("widgets/need_payment_for_education"); ?>
  </div>
</div>