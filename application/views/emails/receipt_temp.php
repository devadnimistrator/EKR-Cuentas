<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
  <head></head>
  <body style="font-family: Verdana;font-size: 12.0px;">
    <div id="__MailbirdStyleContent" style="font-size: 10.0pt;font-family: tahoma;color: rgb(0,0,0);">
      <div>
        <p><br/></p>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tbody>
            <tr>
              <td align="center" bgcolor="#d1ecf9" style="font-family: Zent , Helvetica , sans-serif;padding-bottom: 55.0px;background-color: rgb(209,236,249);" valign="top">
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
                                      <td style="font-size: 106.25%;padding-top: 22.0px;padding-bottom: 22.0px;" valign="top"><b><?php ___("Receipt"); ?> #<?php echo $this->payment_history_m->id; ?></b></td>
                                      <td align="right" style="font-size: 106.25%;font-weight: normal;color: rgb(174,180,183);padding-top: 22.0px;padding-bottom: 22.0px;" valign="top"><span><?php echo my_formart_date($this->payment_history_m->pay_date, DATE_FULL_FORMAT) . " " . $this->payment_history_m->pay_time; ?></span></td>
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
                                                <img src="https://image.izettle.com/profileimage/m/CVBUYU-gaB-YFsMf0VutrJRHOww.jpeg">
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                        <span style="font-size: 137.5%;font-weight: bold;"><?php echo my_show_html_text(RECEIPT_HEADER); ?></span>
                                        <br/>
                                        <a href="https://www.ekrmexico.org.mx" style="color: rgb(27,160,227);text-decoration: none;display: block;margin-top: 5.0px;font-size: 100.0%;" target="_blank">www.ekrmexico.org.mx</a>
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
                                      <td style="font-size: 106.25%;border-bottom: 1.0px solid rgb(216,217,218);padding-bottom: 15.0px;padding-left: 15.0px;padding-right: 15.0px;" valign="top">
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
                                      <td style="font-size: 106.25%;padding-top: 11.0px;padding-bottom: 11.0px;padding-left: 15.0px;padding-right: 15.0px;" valign="top">
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                          <tbody>
                                            <tr>
                                              <td style="padding-top: 5.0px;padding-bottom: 3.0px;" valign="top">
                                                <?php ___("Import"); ?>:
                                              </td>
                                              <td align="right" style="padding-top: 5.0px;padding-bottom: 3.0px;font-size: 162.5%;" valign="top">
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
                                        <table align="center" bgcolor="#f5f6f7" border="0" cellpadding="0" cellspacing="0" style="border-left: 1.0px solid rgb(216,217,218);border-right: 1.0px solid rgb(216,217,218);border-top: 1.0px solid rgb(216,217,218);border-bottom: 1.0px solid rgb(216,217,218);font-size: 100.0%;table-layout: fixed;" width="100%">
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
                            <tr>
                              <td align="center" style="padding-top: 20.0px;padding-bottom: 45.0px;padding-left: 70.0px;padding-right: 70.0px;font-size: 87.5%;color: rgb(167,172,175);line-height: 1.6;" valign="top">
                                <?php echo my_show_html_text(RECEIPT_FOOTER2); ?>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                          <tbody>
                            <tr>
                              <td style="height: 9.0px;background-color: rgb(209,236,249);background-position: bottom left;background-repeat: repeat-x;background-image: url(https://dwsve44av2psn.cloudfront.net/portal/receipt/ripple-new.png);" valign="top"></td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 560.0px;" width="100%">
                  <tbody>
                    <tr>
                      <td valign="top" align="center" style="color: rgb(144,174,187);font-size: 75.0%;padding:30px 0;">
                        Copyright © 2017 Centro TANA
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>