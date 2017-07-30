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
  <div class="col-md-8 col-sm-12 col-xs-12">
    <?php $this->load->view("widgets/calendar"); ?>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#need_payment_for_education"><?php ___("Today"); ?></a></li>
      <li><a data-toggle="tab" href="#last-income-panel"><?php ___("Last Income"); ?></a></li>
      <li><a data-toggle="tab" href="#last-expenses-panel"><?php ___("Last Expenses"); ?></a></li>
    </ul>

    <div class="tab-content">
      <div id="need_payment_for_education" class="tab-pane fade in active">
        <?php $this->load->view("widgets/need_payment_for_education"); ?>
      </div>
      
      <div id="last-income-panel" class="tab-pane fade">
        <?php $this->load->view("widgets/last_income"); ?>
      </div>

      <div id="last-expenses-panel" class="tab-pane fade">
        <?php $this->load->view("widgets/last_expenses"); ?>
      </div>
    </div>
  </div>
</div>

