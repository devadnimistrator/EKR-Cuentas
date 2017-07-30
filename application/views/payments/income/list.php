<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("payments"); ?>"><?php ___("Payments"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Income"); ?> </small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("payments/income/add"); ?>/' + document.getElementById('income_type_id').value"><?php ___("Add New Income"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<style>
  #btnExcelExport.btn-dark {
    cursor: default;
  }

  .text-muted table.table {
    margin-bottom: 0;
  }

  .text-muted table.table tr:first-child td {
    border-top: none;
  }
</style>

<div class="row">
  <div class="col-lg-2 col-md-3 col-xs-12">
    <div class="x_panel">
      <div class="row x_title">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <h2>
            <?php ___("Filters"); ?>
          </h2>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </ul>
          <div class="clearfix"></div>
        </div>
      </div>

      <div class="x_content">
        <div class="row">
          <form method="post" name="frmFilter" id="frmFilter" class="form-filters">
            <label for="payment_status"><?php ___("Status"); ?>:</label>
            <select name="payment_status" id="payment_status" onchange="reloadTable(true)" class="form-control">
              <option value="all">- <?php ___("All"); ?> -</option>
              <option value="<?php echo PAYMENT_STATUS_PAID; ?>" <?php if ($payment_status == PAYMENT_STATUS_PAID) echo "selected"; ?>><?php ___("Paid"); ?></option>
              <option value="<?php echo PAYMENT_STATUS_PENDING; ?>" <?php if ($payment_status == PAYMENT_STATUS_PENDING) echo "selected"; ?>><?php ___("Pending"); ?></option>
            </select>

            <label for="income_type_id"><?php ___("Type"); ?>:</label>
            <select name="income_type_id" id="income_type_id" onchange="changeIncomeType(this.value)" class="form-control">
              <option value="all">- <?php ___("All"); ?> -</option>
              <?php foreach ($this->income_types as $id => $name): ?>
                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>

            <div id="category-filter" style="display: none;">
              <label for="category_id"><?php ___("Category"); ?>:</label>
              <select name="category_id" id="category_id" onchange="reloadTable(true)" class="form-control"></select>
            </div>

            <div id="class-filter" style="display: none;">
              <label for="class_id"><?php ___("Class"); ?>:</label>
              <select name="class_id" id="class_id" onchange="reloadTable(true)" class="form-control"></select>
            </div>

            <label for="payment_method"><?php ___("Payment Method"); ?>:</label>
            <select name="payment_account" id="payment_account" onchange="changeAccount(this.value)" class="form-control">
              <option value="all">- <?php ___("All"); ?> -</option>
              <?php foreach (my_get_payment_methods() as $id => $name): ?>
                <option value="<?php echo $id; ?>" <?php if ($payment_account == $id) echo "selected"; ?>><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>
            <select name="payment_method" id="payment_method" onchange="reloadTable(true)" class="form-control" style="display: none;"></select>

            <label for="payment_method"><?php ___("Date"); ?>:</label>
            <div id="filter_daterange" class="daterangepicker-input">
              <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
              <span><?php echo my_add_date(-29, false, 'M d, y'); ?> - <?php echo date('M d, y'); ?></span>
              <b class="caret"></b>
            </div>
          </form>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-10 col-md-9 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2>
          <?php ___("Histories"); ?>
        </h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><button id="btnExcelExport" type="button" class="btn btn-primary btn-xs" onclick="exportExcel()"><i class="fa fa-file-excel-o"></i> <?php ___("Export"); ?></button></a>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="dataTable" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort" width="30"><i class="fa fa-list-ol"></i></th>
              <th width="100"><?php ___("Registered") ?>:</th>
              <th width="80"><?php ___("Payment") ?>:</th>
              <th width="110" class="nosort"><?php ___("Amount(%s)", my_get_currency_unit()) ?>:</th>
              <th width="90" class="nosort"><?php ___("Paid(%s)", my_get_currency_unit()) ?>:</th>
              <th width="80" class="nosort"><?php ___("Change(%s)", my_get_currency_unit()) ?>:</th>
              <th class="nosort"><?php ___("Reason") ?>:</th>
              <th class="nosort"><?php ___("Status") ?>:</th>
              <th class="nosort" width="100"><?php ___("Actions") ?>:</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="3"></th>
              <th style="text-align:right;"></th>
              <th style="text-align:right;"></th>
              <th style="text-align:right;"></th>
              <th colspan="3"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
  var payment_methods = <?php echo json_encode($this->payment_methods); ?>

  function changeFilter() {
    location.href = "<?php echo base_url("expenses/payments/index"); ?>/" + $("#filter_company").val() + "/" + $("#filter_category").val() + "/" + $("#filter_payment").val() + "/" + $("#from-date").val() + "/" + $("#end-date").val();
  }

  var $dataTable;
  var sumAllAmount = sumAllPaid = "";
  var currencyUnit = '<?php echo my_get_currency_unit(); ?>';
  var income_type = '<?php echo $income_type; ?>';
  var lastSearchParams = {
    start_date: '<?php echo my_add_date(-29); ?>',
    end_date: '<?php echo date('Y-m-d'); ?>'
  };
  $(document).ready(function () {
    $('#filter_daterange').daterangepicker(custom_daterangepicker_option1, function (start, end, label) {
      $("#filter_daterange span").text(start.format('MMM DD, YY') + " - " + end.format('MMM DD, YY'));

      lastSearchParams.start_date = start.format('YYYY-MM-DD');
      lastSearchParams.end_date = end.format('YYYY-MM-DD');

      if (document.frmFilter.income_type_id.value == '<?php echo PAYMENT_REASON_TYPE_EDUCATION; ?>') {
        changeIncomeType(document.frmFilter.income_type_id.value);
      } else {
        reloadTable(true);
      }
    });

    $("#btnExcelExport").removeClass("btn-primary").addClass("btn-dark");

    $dataTable = $('#dataTable').DataTable({
      language: {
        url: "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>",
        searchPlaceholder: "<?php ___("Search by Reason"); ?>"
      },
      columns: [
        {data: "index"},
        {data: "registered"},
        {data: "payment_method"},
        {data: "amount_text", className: "text-right"},
        {data: "paid_amount", className: "text-right"},
        {data: "change", className: "text-right"},
        {data: "reason"},
        {data: "status", className: "text-center"},
        {data: "actions"},
      ],
      order: [[1, "desc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("payments/income/ajax_find"); ?>",
        type: "POST",
        data: function (d) {
          lastSearchParams = $.extend(lastSearchParams, d, {
            payment_status: document.frmFilter.payment_status.value,
            income_type: document.frmFilter.income_type_id.value,
            category_id: document.frmFilter.category_id.value,
            class_id: document.frmFilter.class_id.value,
            payment_account: document.frmFilter.payment_account.value,
            payment_method: document.frmFilter.payment_method.value
          });
//          console.log(lastSearchParams);
          return lastSearchParams;
        },
        dataFilter: function (response) {
          var data = $.parseJSON(response);
          sumAllAmount = data.sumAllAmount;
          sumAllPaid = data.sumAllPaid;
          return response;
        }
      },
      aoColumnDefs: [{
          'bSortable': false,
          'aTargets': ['nosort']
        }],
      responsive: true,
      createdRow: function (row, data, index) {
        $(row).attr('id', data['payment_id']);

        $(row).find('td:eq(4)').text($.number(data.paid_amount, 2));
        if (data.change == 0) {
          $(row).find('td:eq(5)').text('');
        } else {
          $(row).find('td:eq(5)').text($.number(data.change, 2));
        }
      },
      footerCallback: function (row, data, start, end, display) {
        if (data.length == 0) {
        }

        var api = this.api();

        var sumAmount = 0;
        var sumPaid = 0;
        var sumChange = 0;
        for (i = 0; i < data.length; i++) {
          sumAmount += data[i].amount * 1;
          sumPaid += data[i].paid_amount * 1;
          sumChange += data[i].change * 1;
        }

        $(api.column(3).footer()).html('$' + $.number(sumAmount, 2) + '<br/>(' + currencyUnit + $.number(sumAllAmount, 2) + ')');
        $(api.column(4).footer()).html('$' + $.number(sumPaid, 2) + '<br/>(' + currencyUnit + $.number(sumAllPaid, 2) + ')');
        $(api.column(5).footer()).html('$' + $.number(sumChange, 2) + '<br/>(' + currencyUnit + $.number(sumAllPaid - sumAllAmount, 2) + ')');

        $("#btnExcelExport").removeClass("btn-dark").addClass("btn-primary");
      }
    });

    changeAccount($("#payment_account").val());

    if (income_type != 'all') {
      $("#income_type_id").val(income_type);
      changeIncomeType(income_type);
    }
  });

  function reloadTable(resetPaging) {
    $("#btnExcelExport").removeClass("btn-primary").addClass("btn-dark");
    $dataTable.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_payment(id) {
    if (confirm("<?php ___("Are you sure delete selected payment history?"); ?>")) {
      $.get("<?php echo base_url('payments/income/ajax_delete') ?>/" + id, function () {
        reloadTable(false);
      })
    }
  }

  function finish_payment(id) {
    if (confirm("<?php ___("Are you sure finish selected payment history?"); ?>")) {
      $.get("<?php echo base_url('payments/income/ajax_finish') ?>/" + id, function () {
        reloadTable(false);
      })
    }
  }

  function changeIncomeType(type) {
    if (type == '<?php echo PAYMENT_REASON_TYPE_EDUCATION; ?>') {
      $("#category-filter").hide();
      $.post(
              "<?php echo base_url("app/ajax_get_class"); ?>",
              {
                start_date: lastSearchParams.start_date,
                end_date: lastSearchParams.end_date
              },
              function (classes) {
                $("#class_id").html('<option value="all" selected><?php echo ___("All"); ?></option>');

                $.each(classes, function (index, _class) {
                  $("#class_id").append('<option value="' + _class.id + '">' + _class.name + '</option>');
                });
                $("#class-filter").show();

                reloadTable(true);
              },
              'json');
    } else if (type == '<?php echo PAYMENT_REASON_TYPE_TANGIBLE; ?>') {
      $("#class-filter").hide();
      $.getJSON("<?php echo base_url("app/ajax_get_categories"); ?>/" + type, function (categories) {
        $("#category_id").html('<option value="all" selected><?php echo ___("All"); ?></option>');

        $.each(categories, function (index, category) {
          $("#category_id").append('<option value="' + category.id + '">' + category.name + '</option>');
        });
        $("#category-filter").show();

        reloadTable(true);
      });
    } else {
      $("#category-filter").hide();
      $("#class-filter").hide();

      reloadTable(true);
    }
  }

  function changeAccount(account) {
    if (account == 'all') {
      $("#payment_method").hide();
    } else {
      $("#payment_method").html('');

      $("#payment_method").append('<option value="all" selected><?php ___("All"); ?></option>');
      for (var i = 0; i < payment_methods.length; i++) {
        if (payment_methods[i].type == account) {
          $("#payment_method").append('<option value="' + payment_methods[i].id + '">' + payment_methods[i].name + '</option>');
        }
      }
      $("#payment_method").show();
    }

    reloadTable(true);
  }

  function exportExcel() {
    if ($("#btnExcelExport").hasClass("btn-dark")) {
      return;
    }

    var url = "<?php echo base_url("payments/income/ajax_export_excel"); ?>";
    url += "?payment_status=" + lastSearchParams.payment_status;
    url += "&income_type=" + lastSearchParams.income_type;
    url += "&category_id=" + lastSearchParams.category_id;
    url += "&class_id=" + lastSearchParams.class_id;
    url += "&payment_account=" + lastSearchParams.payment_account;
    url += "&payment_method=" + lastSearchParams.payment_method;
    url += "&start_date=" + lastSearchParams.start_date;
    url += "&end_date=" + lastSearchParams.end_date;
    url += "&search=" + encodeURIComponent(lastSearchParams.search.value);

    location.href = url;
  }
</script>

<!-- pay history modal -->
<div id="ShowReceiptButton" data-toggle="modal" data-target="#ReceiptModal"></div>
<div id="ReceiptModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          <?php ___("Receipt"); ?> #<span id="receipt-no"></span>
          <small id="receipt-date" class="pull-right" style="margin-top:10px; color: #999;"></small>
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
        <div style="padding: 0 20px;" id="receipt-info"></div>
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
  <!-- /email address model -->

  <script>
    function show_receipt(id) {
      var selected_income_data = false;

      $dataTable.rows().every(function () {
        var data = this.data();
        if (data.payment_id == id) {
          selected_income_data = data;
          return;
        }
      });

      if (!selected_income_data)
        return;

      $("#receipt-pdf-btn").removeClass('disabled');
      $("#receipt-email-btn").button('reset');

      $("#ReceiptModal").attr("payment_id", id);

      $("#receipt-no").text(selected_income_data.payment_id);
      $("#receipt-date").text(selected_income_data.registered);

      var info = '<table class="table">';
      info += '<thead><tr>';
      info += '<td>' + selected_income_data.reason + '</td>';
      info += '<td class="text-right">$' + $.number(selected_income_data.before_amount, 2) + '</td>';
      info += '</tr></thead>';
      info += '<tbody><tr>';
      info += '<td><?php ___("Import"); ?>:</td>';
      info += '<td class="text-right" style="white-space:nowrap;"><h4>$' + $.number(selected_income_data.before_amount, 2) + '</h4></td>';
      info += '</tr><tr>';
      info += '<td style="border-top: none !important; padding-top: 0 !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  ___("Discount"); ?>(' + selected_income_data.discount + ')</td>';
      info += '<td style="border-top: none !important; padding-top: 0 !important;" class="text-right" style="white-space:nowrap;">$' + $.number(selected_income_data.discount_amount, 2) + '</td>';
      info += '</tr></tbody>';
      info += '</table>';
      info += '<div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">';
      info += '<table style="width: 100%;" class="table">';
      info += '<tr><td>' + selected_income_data.payment_method + '</td>';
      info += '<td class="text-right" style="white-space:nowrap;">$' + $.number(selected_income_data.paid_amount, 2) + '</td></tr>';
      if (selected_income_data.change > 0) {
        info += '<tr><td><?php ___("Change"); ?></td>';
        info += '<td class="text-right" style="white-space:nowrap;">$' + $.number(selected_income_data.change, 2) + '</td></tr>';
      }
      info += '</table>';
      info += '</div>';

      $("#receipt-info").html(info);

      if (selected_income_data.is_enable_email) {
        $("#show-email-btn").show();
      } else {
        $("#show-email-btn").hide();
      }

      $("#ShowReceiptButton").click();
    }

    $(function () {
      $("#receipt-pdf-btn").click(function () {
        if ($(this).hasClass('disabled')) {
          return;
        }
        var payment_id = $("#ReceiptModal").attr("payment_id");
        location.href = "<?php echo site_url("payments/income/ajax_receipt_pdf"); ?>/" + payment_id;
      });

      $("#show-email-btn").click(function () {
        var payment_id = $("#ReceiptModal").attr("payment_id");
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
      })

      $("#receipt-email-btn").click(function () {
        if ($("#receiver_email").val().length == 0) {
          $("#sent-email-message").removeClass("alert-success").addClass("alert-danger").show();
          $("#sent-email-message").html("<?php ___("Please input receiver's email."); ?>");
          
          $("#receiver_email").focus();
          
          return;
        }

        $("#sent-email-message").hide();

        var payment_id = $("#ReceiptModal").attr("payment_id");

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