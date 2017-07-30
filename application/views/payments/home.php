<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-12">
    <div class="x_content">
      <div class="row">
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_cash', array('show_chart' => false)); ?>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_income', array('show_chart' => false)); ?>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_bank', array('show_chart' => false)); ?>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_caja', array('show_chart' => false)); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-sm-12 col-xs-12">
    <?php $this->load->view("widgets/last_income"); ?>
  </div>

  <div class="col-md-6 col-sm-12 col-xs-12">
    <?php $this->load->view("widgets/last_expenses"); ?>
  </div>
</div>