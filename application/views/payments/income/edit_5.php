<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("payments"); ?>"><?php ___("Payments"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("payments/income"); ?>"><?php ___("Income"); ?></a> </small>
      <?php if ($this->payment_history_m->is_exists()): ?>
        <small><i class="fa fa-angle-double-right"></i> <?php ___("Edit Income"); ?> #<?php echo $this->payment_history_m->id; ?></small>
      <?php else: ?>
        <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("payments/income/add/first"); ?>"><?php ___("New regist"); ?></a></small>
        <small><i class="fa fa-angle-double-right"></i> <?php ___("Step2"); ?></small>
      <?php endif; ?>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <?php if ($this->payment_history_m->is_exists()): ?>
        <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("payments/income/add/first"); ?>'"><?php ___("Add New"); ?></button>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class=" col-sm-12 col-xs-12">
    <?php
    my_show_msg($error_msgs, "danger");

    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "editPayment",
    "autocomplete" => false,
    "col_width" => 2
);

$bsForm = new My_bs_form($formConfig);
$bsForm->form_start(TRUE);
?>
<input type="hidden" name="action" value="process" />

<div class="row">
  <div class="col-lg-6 col-md-9 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>
          <?php ___("Movement Transaction"); ?>
          <small><?php ___("from Caja Chica to Bank"); ?></small>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="item form-group">
          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-amount"><?php ___("Amount(%s)", my_get_currency_unit()); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12">
            <input type="number" id="em-amount" name="amount" value="<?php echo $this->payment_history_m->amount; ?>" required="required" class="form-control" autocomplete="off">
          </div>
        </div>

        <div class="item form-group">
          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-from_payment_method"><?php ___("From"); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input">
            <select id="em-from_payment_method" name="from_payment_method" required="required" class="form-control col-xs-12">
              <?php foreach ($this->payment_methods as $payment_method): if ($payment_method->type == PAYMENT_METHOD_BANK) continue; ?>
                <option value="<?php echo $payment_method->id; ?>" <?php if ($payment_method->id == $this->payment_history_m->from_payment_method) echo "selected"; ?>><?php echo $payment_method->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-to_payment_method"><?php ___("To"); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input">
            <select id="em-to_payment_method" name="to_payment_method" required="required" class="form-control col-xs-12">
              <?php foreach ($this->payment_methods as $payment_method): if ($payment_method->type == PAYMENT_METHOD_CAJA) continue; ?>
                <option value="<?php echo $payment_method->id; ?>" <?php if ($payment_method->id == $this->payment_history_m->to_payment_method) echo "selected"; ?>><?php echo $payment_method->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="item form-group">
          <label class = "control-label col-md-2 col-sm-2 col-xs-12" for = "em-pay_date"><?php ___("Registered"); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12">
            <div class=" input-group">
              <?php
              $pay_date = date(DATE_FULL_FORMAT);
              if ($this->payment_history_m->pay_date) {
                $pay_date = my_formart_date($this->payment_history_m->pay_date, DATE_FULL_FORMAT);
              }
              ?>
              <input type="text" id="em-pay_date" name="pay_date" value="<?php echo $pay_date; ?>" required="required" class="form-control date-picker" autocomplete="off" size="10">
              <?php
              $pay_h = substr($this->payment_history_m->pay_time, 0, 2);
              $pay_m = substr($this->payment_history_m->pay_time, 3, 2);
              ?>
              <div class="input-group-btn" style="width: 100px; padding-left: 8px;">
                <select name="pay_time_h" required="required" class="form-control" style="width: 42px; float: left; padding-left: 2px !important; padding-right: 0px !important;">
                  <?php
                  for ($h = 0; $h < 24; $h ++): $_h = str_pad($h, 2, "0", STR_PAD_LEFT);
                    ?>
                    <option value="<?php echo $_h; ?>" <?php if ($_h == $pay_h) echo "selected" ?>><?php echo $h; ?></option>
                  <?php endfor; ?>
                </select>
                <div style="float: left; padding: 5px 0; width: 8px; text-align: center; font-size: 14px;">:</div>
                <select name="pay_time_m" required="required" class="form-control" style="width: 42px; float: left; padding-left: 2px !important; padding-right: 0px !important;">
                  <?php
                  for ($m = 0; $m < 60; $m ++): $_m = str_pad($m, 2, "0", STR_PAD_LEFT);
                    ?>
                    <option value="<?php echo $_m; ?>" <?php if ($_m == $pay_m) echo "selected" ?>><?php echo $_m; ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="ln_solid"></div>

        <?php
        $bsForm->form_buttons(TRUE);
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<?php
$bsForm->form_end(TRUE);
?>
