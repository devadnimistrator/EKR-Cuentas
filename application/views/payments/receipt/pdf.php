<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
  <head>
    <title><?php ___("Receipt"); ?> #<?php echo $this->payment_history_m->id; ?></title>
    <!-- for-mobile-apps -->
    <meta name="viewport" content="width=1024px, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
  <body style="font-family:Verdana; font-size: 12.0px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
      <tbody>
        <tr>
          <td align="center" style="font-family: Zent, Helvetica, sans-serif; padding-bottom: 55.0px;" valign="top">
            <br/><br/><br/>
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 560.0px;" width="100%">
              <tbody>
                <tr>
                  <td bgcolor="white" valign="top">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td style="padding-left: 30.0px;padding-right: 30.0px;" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody>
                                <tr>
                                  <td style="font-size:13px; padding-top:22.0px; padding-bottom:22.0px;" valign="top"><b><?php ___("Receipt"); ?> #<?php echo $this->payment_history_m->id; ?></b></td>
                                  <td align="right" style="font-size:13px; font-weight:normal; color: rgb(174,180,183); padding-top: 22.0px; padding-bottom: 22.0px;" valign="top"><span><?php echo my_formart_date($this->payment_history_m->pay_date, false) . " " . $this->payment_history_m->pay_time; ?></span></td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody>
                                <tr>
                                  <td align="center" style="padding-top: 30.0px;padding-bottom: 35.0px;border-top: 1.0px solid rgb(216,217,218);border-bottom: 1.0px solid rgb(216,217,218);" valign="top">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                      <tbody>
                                        <tr>
                                          <td align="center" style="padding-bottom: 20.0px;" valign="top">
                                            <img src="<?php echo dirname(APPPATH) . "/assets/images/logo3.jpeg"; ?>" />
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <span style="font-size: 14px;font-weight: bold;"><?php echo my_show_html_text(RECEIPT_HEADER); ?></span>
                                    <br/>
                                    <a href="https://www.ekrmexico.org.mx" style="color: rgb(27,160,227);text-decoration: none;display: block;margin-top: 5.0px;" target="_blank">www.ekrmexico.org.mx</a>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td style="padding-top: 40.0px;padding-bottom: 10.0px;padding-left: 50.0px;padding-right: 50.0px;" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody>
                                <tr>
                                  <td style="border-bottom: 1.0px solid rgb(216,217,218);padding-bottom: 15.0px;padding-left: 15.0px;padding-right: 15.0px;" valign="top">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                      <tbody>
                                        <tr>
                                          <td style="padding-top: 6.0px;padding-bottom: 6.0px;" valign="top">
                                            <?php echo $this->income_types[$this->payment_history_m->reason_type] . " / " . $this->payment_history_m->reason_desc; ?>
                                          </td>
                                          <td align="left" style="padding-top: 6.0px;padding-bottom: 6.0px;padding-left: 15.0px;white-space: nowrap;" valign="top"></td>
                                          <td align="right" style="padding-top: 6.0px;padding-bottom: 6.0px;padding-left: 15.0px;white-space: nowrap;" valign="top">
                                            $<?php echo number_format($this->payment_history_m->before_amount, 2); ?>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody>
                                <tr>
                                  <td style="padding-top: 11.0px;padding-bottom: 11.0px;padding-left: 15.0px;padding-right: 15.0px;" valign="top">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                      <tbody>
                                        <tr>
                                          <td style="padding-top: 5.0px;padding-bottom: 3.0px;" valign="top">
                                            <?php ___("Import"); ?>:
                                          </td>
                                          <td align="right" style="padding-top: 5.0px;padding-bottom: 3.0px;font-size: 14px;" valign="top">
                                            $<?php echo number_format($this->payment_history_m->before_amount, 2); ?>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td style="padding-top: 5.0px;padding-bottom: 3.0px;color: rgb(174,180,183);padding-left: 25.0px;" valign="top">
                                            <?php  ___("Discount"); ?>(<?php echo $this->payment_history_m->discount_type == 'percent' ? $this->payment_history_m->discount . "%" : "$" . $this->payment_history_m->discount; ?>)
                                          </td>
                                          <td align="right" style="padding-top: 5.0px;padding-bottom: 3.0px;color: rgb(174,180,183);" valign="top">
                                            $<?php echo number_format($this->payment_history_m->before_amount - $this->payment_history_m->amount, 2); ?>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody>
                                <tr>
                                  <td style="padding-top: 25.0px;" valign="top">
                                    <table align="center" bgcolor="#f5f6f7" border="0" cellpadding="0" cellspacing="0" style="border-left: 1.0px solid rgb(216,217,218);border-right: 1.0px solid rgb(216,217,218);border-top: 1.0px solid rgb(216,217,218);border-bottom: 1.0px solid rgb(216,217,218);table-layout: fixed;" width="100%">
                                      <tbody>
                                        <tr>
                                          <td style="border-top: none;padding-top: 15.0px;padding-bottom: 15.0px;padding-left: 15.0px;padding-right: 15.0px;vertical-align: bottom;width: 70.0%;" valign="top">
                                            <?php echo $this->payment_methods[$this->payment_history_m->payment_method_id]; ?>
                                          </td>
                                          <td align="right" style="border-top: none;padding-top: 15.0px;padding-bottom: 15.0px;padding-left: 15.0px;padding-right: 15.0px;vertical-align: bottom;" valign="top">
                                            $<?php echo number_format($this->payment_history_m->paid_amount, 2); ?>
                                          </td>
                                        </tr>
                                        <?php if ($this->payment_history_m->paid_amount != $this->payment_history_m->amount): ?>
                                          <tr>
                                            <td style="border-top: 1px solid #ccc;padding-top: 15.0px;padding-bottom: 15.0px;padding-left: 15.0px;padding-right: 15.0px;vertical-align: bottom;width: 70.0%;" valign="top">
                                              <?php ___("Change"); ?>
                                            </td>
                                            <td align="right" style="border-top: 1px solid #ccc;padding-top: 15.0px;padding-bottom: 15.0px;padding-left: 15.0px;padding-right: 15.0px;vertical-align: bottom;" valign="top">
                                              $<?php echo number_format($this->payment_history_m->paid_amount - $this->payment_history_m->amount, 2); ?>
                                            </td>
                                          </tr>
                                        <?php endif; ?>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td align="center" style="border-bottom: 1.0px solid rgb(216,217,218);padding-top: 20.0px;padding-bottom: 20.0px;" valign="top">
                            <b><?php echo my_show_html_text(RECEIPT_FOOTER1); ?></b>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td align="center" style="padding-top: 20.0px;padding-bottom: 45.0px;font-size: 11px;color: rgb(167,172,175);line-height: 1.6;" valign="top">
                            <?php echo my_show_html_text(RECEIPT_FOOTER2); ?>
                          </td>
                        </tr>
                        <tr>
                          <td align="center" style="color:rgb(144,174,187); font-size:11px; padding-top:30px;">
                            Copyright © 2017 Centro TANA
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>