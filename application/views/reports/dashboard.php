<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-12">
    <div class="x_content">
      <div class="row">
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_cash'); ?>

          <div class="text-center">
            <button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/chart/cash"); ?>'">
              <i class="fa fa-area-chart"></i> <?php ___("View Chart"); ?>
            </button>
            <button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/grid/cash"); ?>'">
              <i class="fa fa-list"></i> <?php ___("View List"); ?>
            </button>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_bank'); ?>

          <div class="text-center">
            <button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/chart/cash/" . PAYMENT_METHOD_BANK); ?>'">
              <i class="fa fa-area-chart"></i> <?php ___("View Chart"); ?>
            </button>
            <button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/grid/cash/" . PAYMENT_METHOD_BANK); ?>'">
              <i class="fa fa-list"></i> <?php ___("View List"); ?>
            </button>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_caja'); ?>

          <div class="text-center">
            <button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/chart/cash/" . PAYMENT_METHOD_CAJA); ?>'">
              <i class="fa fa-area-chart"></i> <?php ___("View Chart"); ?>
            </button>
            <button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/grid/cash/" . PAYMENT_METHOD_CAJA); ?>'">
              <i class="fa fa-list"></i> <?php ___("View List"); ?>
            </button>
          </div>
        </div>

        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <?php $this->load->view('widgets/total_income'); ?>

          <div class="text-center">
            <button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/chart/cash#chart-income"); ?>'">
              <i class="fa fa-area-chart"></i> <?php ___("View Chart"); ?>
            </button>
            <!--button type="button" class="btn btn-default btn-lg" onclick="location.href = '<?php echo base_url("reports/grid/income"); ?>'">
              <i class="fa fa-list"></i> <?php ___("View List"); ?>
            </button-->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$this->load->view('js/echart');
