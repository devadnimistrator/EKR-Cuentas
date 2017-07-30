<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<form class="form-horizontal form-label-left validateform" novalidate method="post">
  <input type="hidden" name="action" value="process" />
  <div class="row">
    <?php my_show_system_message("success"); ?>
    
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("System Configuration"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <p>
            <?php ___("Please configuration for system."); ?>
          </p>
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="site_title"><?php ___("Site Title"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <input id="site_title" name="site_title" value="<?php echo SITE_TITLE ?>" class="form-control col-md-12 col-xs-12" required="required" type="text">
            </div>
          </div>
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contact_email"><?php ___("Contact Email"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <input type="text" id="contact_email" name="contact_email" value="<?php echo CONTACT_EMAIL ?>" required="required" class="form-control col-md-12 col-xs-12">
            </div>
          </div>
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contact_phone"><?php ___("Contact Phone"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <input id="contact_phone" name="contact_phone" value="<?php echo CONTACT_PHONE ?>" required="required" class="form-control col-md-12 col-xs-12">
            </div>
          </div>
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contact_street"><?php ___("Contact Street"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <textarea id="contact_street" name="contact_street" required="required" class="form-control col-md-12 col-xs-12"><?php echo CONTACT_STREET ?></textarea>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="x_title">
          <h2><?php ___("Location Configuration"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="default_country"><?php ___("Country"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <select name="default_country" id="default_country" class="form-control col-md-12 col-xs-12">
                <option value="MX" <?php if (DEFAULT_COUNTRY == 'MX') echo "selected" ?>><?php ___("Mexico"); ?></option>
                <option value="US" <?php if (DEFAULT_COUNTRY == 'US') echo "selected" ?>><?php ___("United States"); ?></option>
              </select>
            </div>
          </div>
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="display_language"><?php ___("Language"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <select name="display_language" id="display_language" class="form-control col-md-12 col-xs-12">
                <option value="english" <?php if (DISPLAY_LANGUAGE == 'english') echo "selected" ?>><?php ___("English"); ?></option>
                <option value="spanish" <?php if (DISPLAY_LANGUAGE == 'spanish') echo "selected" ?>><?php ___("Spanish"); ?></option>
              </select>
            </div>
          </div>
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="default_currency"><?php ___("Currency"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <select name="default_currency" id="default_currency" class="form-control col-md-12 col-xs-12">
                <option value="peso" <?php if (DEFAULT_CURRENCY_UNIT == 'peso') echo "selected" ?>><?php ___("MXN"); ?></option>
                <option value="usd" <?php if (DEFAULT_CURRENCY_UNIT == 'usd') echo "selected" ?>><?php ___("USD"); ?></option>
              </select>
            </div>
          </div>
          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="reminder_date"><?php ___("Reminder Date"); ?> <span class="required">*</span> </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
              <input type="number" id="reminder_date" name="reminder_date" value="<?php echo REMINDER_DATE ?>" required="required" class="form-control col-md-12 col-xs-12">
            </div>
          </div>
          <div class="ln_solid"></div>
          <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
              <button type="reset" class="btn btn-default">
                <?php ___("Cancel"); ?>
              </button>
              <button id="send" type="submit" class="btn btn-primary">
                <?php ___("Submit"); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2><?php ___("Receipt Email Template"); ?></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="text-center">
            <img src="<?php echo base_url("assets/images/logo3.jpeg"); ?>" />
          </div>
          <div class="item form-group">
            <label for="receipt_header"><?php ___("Header"); ?> <span class="required">*</span> </label>
            <textarea id="receipt_header" name="receipt_header" required="required" class="form-control text-center"><?php echo RECEIPT_HEADER ?></textarea>
          </div>
          
          <div class="text-center">
            <a href="https://www.ekrmexico.org.mx">www.ekrmexico.org.mx</a>
          </div>
          
          <div class="ln_solid"></div>
          
          <div style="padding: 0 20px;" id="receipt-info">
            (* <?php ___("Example"); ?>)
            <table class="table">
              <thead>
                <tr>
                  <td>Education Program</td>
                  <td class="text-right">$150.00</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><?php ___("Import"); ?>:</td>
                  <td class="text-right"><h4>$150.00</h4></td>
                </tr>
                <tr>
                  <td style="border-top: none !important; padding-top: 0 !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  ___("Discount"); ?>(10.00%)</td>
                  <td style="border-top: none !important; padding-top: 0 !important;" class="text-right">$15.00</td>
                </tr>
              </tbody>
            </table>
            <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
              <table style="width: 100%;">
                <tbody>
                  <tr>
                    <td>Check * &lt; PAYMENT METHOD</td>
                    <td class="text-right">PAID &gt; $135.00</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="ln_solid"></div>
          
          <div class="item form-group">
            <label for="receipt_footer1"><?php ___("Footer1"); ?> <span class="required">*</span> </label>
            <textarea id="receipt_footer1" rows=5 name="receipt_footer1" required="required" class="form-control text-center"><?php echo RECEIPT_FOOTER1 ?></textarea>
          </div>
          
          <div class="item form-group">
            <label for="receipt_footer2"><?php ___("Footer2"); ?> <span class="required">*</span> </label>
            <textarea id="receipt_footer2" rows=5 name="receipt_footer2" required="required" class="form-control text-center"><?php echo RECEIPT_FOOTER2 ?></textarea>
          </div>
          
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>
</form>