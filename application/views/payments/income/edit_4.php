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
        <h2><?php ___("Payment Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="item form-group" id="form-group-reason">
          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-reason">Reason <span class="required">*</span></label>
          <div class="col-md-10 col-sm-10 col-xs-12 control-input">
            <textarea id="em-reason" name="reason" required="required" rows="5" class="form-control"><?php echo $this->payment_history_m->reason_desc; ?></textarea>
          </div>
        </div>

        <div class="item form-group" id="form-group-before_amount">
          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-before_amount"><?php ___("Amount(%s)", my_get_currency_unit()); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="input-group">
              <input type="number" id="em-before_amount" name="before_amount" value="<?php echo $this->payment_history_m->before_amount; ?>" required="required" class="form-control" autocomplete="off" onchange="calcAmount()">
              <div class="input-group-addon">to</div>
              <input type="number" id="em-amount" name="amount" value="<?php echo $this->payment_history_m->amount; ?>" class="form-control" required="required" autocomplete="off" readonly>
            </div>
          </div>
          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-discount"><?php ___("Discount"); ?> <span class="required">:</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="input-group">
              <input type="number" id="em-discount" name="discount" value="<?php echo $this->payment_history_m->discount; ?>" class="form-control" autocomplete="off" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_NO) echo "disabled"; ?> onchange="calcAmount()">
              <div class="input-group-btn">
                <select name="discount_type" id="em-discount_type" class="form-control" style="width: 100px; padding-left: 3px; padding-right: 0;" onchange="chooiseDicsountType(this.value)">
                  <option value="<?php echo DISCOUNT_TYPE_NO; ?>" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_NO) echo "selected"; ?>><?php ___("No"); ?></option>
                  <option value="<?php echo DISCOUNT_TYPE_FIXED; ?>" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_FIXED) echo "selected"; ?>><?php ___("(%s)Fixed", my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string")); ?></option>
                  <option value="<?php echo DISCOUNT_TYPE_PERCENT; ?>" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_PERCENT) echo "selected"; ?>><?php ___("(%)Percent"); ?></option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="item form-group">
          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-payment_method_id"><?php ___("Method"); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input">
            <select id="em-payment_method_id" name="payment_method_id" required="required" class="form-control col-xs-12">
              <?php foreach ($this->payment_methods as $id => $name): ?>
                <option value="<?php echo $id; ?>" <?php if ($id == $this->payment_history_m->payment_method_id) echo "selected"; ?>><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

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
        
        <div class="item form-group">
          <label class="control-label col-md-2 col-sm-2 col-xs-12" for="em-payment_method_id"><?php ___("Status"); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input">
            <select id="em-payment_status" name="payment_status" required="required" class="form-control col-xs-12">
              <option value="<?php echo PAYMENT_STATUS_PAID; ?>" <?php if (PAYMENT_STATUS_PAID == $this->payment_history_m->status) echo "selected"; ?>><?php ___("Paid"); ?></option>
              <option value="<?php echo PAYMENT_STATUS_PENDING; ?>" <?php if (PAYMENT_STATUS_PENDING== $this->payment_history_m->status) echo "selected"; ?>><?php ___("Pending"); ?></option>
            </select>
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

<script>
  function chooiseDicsountType(discount_type) {
    if (discount_type == '<?php echo DISCOUNT_TYPE_NO; ?>') {
      $("#em-discount").val('0.00');
      $("#em-discount").prop('disabled', true);
    } else {
      $("#em-discount").prop('disabled', false);
    }

    calcAmount();
  }

  function calcAmount() {
    var before_amount = $("#em-before_amount").val() * 1;
    if (before_amount <= 0) {
      $("#em-before_amount").val("");
      $("#em-amount").val("");
      $("#form-group-before_amount").addClass('bad');
      return;
    }

    var discount_type = $("#em-discount_type").val();
    $("#em-before_amount").val(before_amount);

    var discount = $("#em-discount").val() * 1;
    $("#em-discount").val(discount);
    if (discount < 0) {
      $("#em-amount").val("");
      $("#form-group-before_amount").addClass('bad');
      return;
    }
    if (discount_type == '<?php echo DISCOUNT_TYPE_NO; ?>') {
      $("#em-amount").val(before_amount);
    } else if (discount_type == '<?php echo DISCOUNT_TYPE_FIXED; ?>') {
      if (discount >= before_amount) {
        $("#em-amount").val("");
        $("#form-group-before_amount").addClass('bad');
        return;
      }

      $("#em-amount").val(before_amount - discount);
    } else if (discount_type == '<?php echo DISCOUNT_TYPE_PERCENT; ?>') {
      if (discount > 90) {
        $("#em-amount").val("");
        $("#form-group-before_amount").addClass('bad');
        return;
      }

      $("#em-amount").val(before_amount * (100.00 - discount) / 100.00);
    }
  }
</script>
