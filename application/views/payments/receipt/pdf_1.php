<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
  <head>
    <title><?php ___("Receipt"); ?> #<?php echo $this->payment_history_m->id; ?></title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=1024px, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>

  <body style="font-family: 'Open Sans', sans-serif; font-size: 14px;">
    <table width="100%">
      <tr>
        <td>
          <h3><?php ___("Receipt"); ?> #<?php echo $this->payment_history_m->id; ?></h3>
        </td>
        <td align="right"><?php echo $this->payment_history_m->pay_date . " " . $this->payment_history_m->pay_time; ?></td>
      </tr>
    </table>

    <div style="width:100%; height: 0; border-top: 1px solid #ccc;"></div>

    <br/><br/><br/>

    <div style="text-align: center;">
      <img src="<?php echo dirname(APPPATH) . "/assets/images/logo3.jpeg"; ?>" />
      <h2><?php echo my_show_html_text(RECEIPT_HEADER); ?></h2>
      <a href="https://www.ekrmexico.org.mx">www.ekrmexico.org.mx</a>
    </div>

    <br/><br/><br/>

    <div style="width:100%; height: 0; border-top: 1px solid #ccc;"></div>

    <br/><br/><br/>

    <div style="width: 90%; margin: 0 auto;">
      <table style="width:90%;" align="center">
        <tr>
          <td><?php echo $this->income_types[$this->payment_history_m->reason_type] . " / " . $this->payment_history_m->reason_desc; ?></td>
          <td align="right">$<?php echo number_format($this->payment_history_m->before_amount, 2); ?></td>
        </tr>
      </table>

      <br/>

      <div style="width:100%; height: 0; border-top: 1px solid #ccc;"></div>

      <table style="width:90%;" align="center">
        <tr>
          <td><?php ___("Import"); ?></td>
          <td align="right"><h2>$<?php echo number_format($this->payment_history_m->before_amount, 2); ?></h2></td>
        </tr>
      </table>

      <table style="width:90%; font-size: 16px; color: #999;" align="center">
        <tr>
          <td>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            IVA(<?php echo $this->payment_history_m->discount_type == 'percent' ? $this->payment_history_m->discount . "%" : "$" . $this->payment_history_m->discount; ?>);
          </td>
          <td align="right">$<?php echo number_format($this->payment_history_m->before_amount - $this->payment_history_m->amount, 2); ?></td>
        </tr>
      </table>

      <br/><br/><br/>

      <div style="width: 100%; border: 1px solid #ccc; background: #eee; text-transform: uppercase;">
        <table style="width:90%; font-size: 16px;" align="center">
          <tr>
            <td style="padding: 8px 0;">
              <?php echo $this->payment_methods[$this->payment_history_m->payment_method_id]; ?>
            </td>
            <td style="padding: 8px 0;" align="right">
              $<?php echo number_format($this->payment_history_m->paid_amount, 2); ?>
            </td>
          </tr>
          <?php if ($this->payment_history_m->paid_amount != $this->payment_history_m->amount): ?>
          <tr>
            <td style="padding: 8px 0; border-top: 1px solid #ccc;">
              <?php ___("Change"); ?>
            </td>
            <td style="padding: 8px 0; border-top: 1px solid #ccc;" align="right">
              $<?php echo number_format($this->payment_history_m->paid_amount - $this->payment_history_m->amount, 2); ?>
            </td>
          </tr>
          <?php endif; ?>
        </table>
      </div>
    </div>

    <br/><br/><br/>

    <div style="width:50%; margin: 0 auto; text-align: center;">
      <strong><?php echo my_show_html_text(RECEIPT_FOOTER1); ?></strong>

      <br/><br/>

      <div style="width:100%; height: 0; border-top: 1px solid #ccc"></div>

      <br/><br/>

      <small style="color: #999;"><?php echo my_show_html_text(RECEIPT_FOOTER2); ?></small>
    </div>
  </body>
</html>