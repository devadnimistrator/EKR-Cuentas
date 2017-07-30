<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- pay history modal -->
<div id="ReceiptModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          <?php ___("Receipt"); ?> #<span><?php echo $payment_m->id; ?></span>
          <small id="receipt-date" class="pull-right" style="margin-top:10px; color: #999;"><?php echo my_formart_date($payment_m->pay_date, DATE_FULL_FORMAT) . " " . $payment_m->pay_time; ?></small>
        </h4>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <img src="<?php echo base_url("assets/images/logo3.jpeg"); ?>" />
          <h3><?php echo my_show_html_text(RECEIPT_HEADER); ?></h3>
          <a href="https://www.ekrmexico.org.mx">www.ekrmexico.org.mx</a>
        </div>
      </div>

      <div class="ln_solid"></div>

      <div class="modal-body">
        <div style="padding: 0 20px;">
          <table class="table">
            <thead>
              <tr>
                <td><?php echo $payment_m->reason . " / " . $payment_m->reason_desc; ?></td>
                <td class="text-right"><?php my_show_amount($payment_m->before_amount); ?></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php ___("Import"); ?>:</td>
                <td class="text-right" style="white-space:nowrap;"><h4><?php my_show_amount($payment_m->before_amount); ?></h4></td>
              </tr>
              <tr>
                <td style="border-top: none !important; padding-top: 0 !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  ___("Discount"); ?>(<?php echo $payment_m->discount_type == 'percent' ? $payment_m->discount . "%" : my_get_currency_unit() . $payment_m->discount; ?>)</td>
                <td style="border-top: none !important; padding-top: 0 !important; white-space:nowrap;" class="text-right"><?php my_show_amount($payment_m->before_amount - $payment_m->amount); ?></td>
              </tr>
            </tbody>
          </table>
          <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            <table style="width: 100%;margin-bottom: 0; font-size: 14px;" class="table">
              <tbody>
                <tr>
                  <td style="border-top: none;"><?php echo $payment_m->payment_method ?></td>
                  <td style="border-top: none; white-space:nowrap;" class="text-right"><?php my_show_amount($payment_m->paid_amount); ?></td>
                </tr>
                <?php if ($payment_m->paid_amount != $payment_m->amount): ?>
                  <tr>
                    <td><?php ___("Change"); ?></td>
                    <td class="text-right" style="white-space:nowrap;"><?php my_show_amount($payment_m->paid_amount - $payment_m->amount); ?></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="text-center" style="padding: 0 60px 40px;">
          <strong><?php echo my_show_html_text(RECEIPT_FOOTER1); ?></strong>
          <div class="ln_solid"></div>
          <small style="color: #999;"><?php echo my_show_html_text(RECEIPT_FOOTER2); ?></small>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-primary pull-left" id="receipt-pdf-btn"><i class="fa fa-file-pdf-o"></i> <?php ___("Generate PDF"); ?></button>
        <button type="button" class="btn btn-default btn-primary pull-left" id="show-email-btn"><i class="fa fa-envelope-o"></i> <?php ___("Send Email"); ?></button>
        <button type="button" class="btn btn-default antoclose" data-dismiss="modal"><i class="fa fa-close"></i> <?php ___("Close"); ?></button>
      </div>
    </div>
  </div>
</div>
<!-- /pay history modal -->

<!-- email address model -->
<div id="ShowEmailAddressButton" data-toggle="modal" data-target="#EmailAddressModal"></div>
<div id="EmailAddressModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="margin-top: 15vh;">
  <div class="modal-dialog" style="width: 300px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">
          <?php ___("Please input receiver's email."); ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-dismissible fade in" role="alert" id="sent-email-message" style="display: none;"></div>
        <div class="input-fields"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-primary" id="receipt-email-btn" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php ___("Sending Email"); ?>"><i class="fa fa-send"></i> <?php ___("Send"); ?></button>
      </div>
    </div>
  </div>
  <!-- /email address modal -->

  <script>
    var payment_id = "<?php echo $payment_m->id; ?>";
    $(function () {
      $("#receipt-pdf-btn").click(function () {
        if ($(this).hasClass('disabled')) {
          return;
        }
        var payment_id = $("#ReceiptModal").attr("payment_id");
        location.href = "<?php echo site_url("payments/income/ajax_receipt_pdf"); ?>/" + payment_id;
      });

      $("#show-email-btn").click(function () {
        $("#sent-email-message").hide();
        $.getJSON("<?php echo site_url("payments/income/ajax_get_receiver"); ?>/" + payment_id, function (receiver) {
          var $emailBody = $("#EmailAddressModal .modal-body .input-fields");
          $emailBody.html('');
          if (receiver.name.length > 0) {
            $emailBody.append("<label>(<?php ___("To"); ?>: " + receiver.name + ")</label><br/>");
          }
          $emailBody.append('<input type="email" id="receiver_email" value="' + receiver.email + '" class="form-control" />');
          $("#ShowEmailAddressButton").click();
        })
      });

      $("#receipt-email-btn").click(function () {
        if ($("#receiver_email").val().length == 0) {
          $("#sent-email-message").removeClass("alert-success").addClass("alert-danger").show();
          $("#sent-email-message").html("<?php ___("Please input receiver's email."); ?>");

          $("#receiver_email").focus();

          return;
        }

        $("#sent-email-message").hide();

        var $this = $(this);
        $this.button('loading');

        $.ajax({
          method: "POST",
          url: "<?php echo site_url("payments/income/ajax_receipt_email"); ?>",
          data: {
            "payment_id": payment_id,
            "receiver_email": $("#receiver_email").val()
          }
        }).done(function (status) {
          if (status == 'OK') {
            $("#sent-email-message").html("<?php ___("Successfully sent email."); ?>");
            $("#sent-email-message").removeClass("alert-danger").addClass("alert-success").show();
          } else {
            $("#sent-email-message").removeClass("alert-success").addClass("alert-danger").show();
            $("#sent-email-message").html("<?php ___("Failed to send email."); ?>");
          }
        }).fail(function () {
          $("#sent-email-message").removeClass("alert-success").addClass("alert-danger").show();
          $("#sent-email-message").html("<?php ___("Failed to send email."); ?>");
        }).always(function () {
          $this.button('reset');
        });
      });
    })
  </script>