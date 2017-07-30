<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <?php ___("Payments"); ?>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Expenses"); ?> </small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("payments/expenses/add"); ?>'"><?php ___("Add New Expenses"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<style>
  #btnExcelExport.btn-dark {
    cursor: default;
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
            <label for="company_id"><?php ___("Company"); ?>:</label>
            <select name="company_id" id="company_id" onchange="reloadTable(true)" class="form-control">
              <option value="all">- <?php ___("All"); ?> -</option>
              <?php foreach ($this->companies as $id => $name): ?>
                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>

            <label for="category_id"><?php ___("Category"); ?>:</label>
            <select name="category_id" id="category_id" onchange="reloadTable(true)" class="form-control">
              <option value="all">- <?php ___("All"); ?> -</option>
              <?php foreach ($this->categories as $id => $name): ?>
                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>          

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
              <span><?php echo my_add_date(-29, false, 'M d, y'); ?> - <?php echo date('M d, Y'); ?></span>
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
          <li><button id="btnExcelExport" type="button" class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-file-excel-o"></i> <?php ___("Export"); ?></button></a>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="dataTable" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Registered") ?>:</th>
              <th><?php ___("Payment") ?>:</th>
              <th><?php ___("Amount(%s)", my_get_currency_unit()) ?>:</th>
              <th><?php ___("Company") ?>:</th>
              <th><?php ___("Category") ?>:</th>
              <th><?php ___("Product") ?>:</th>
              <th class="nosort"><?php ___("Actions") ?>:</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="4" style="text-align:right"></th>
              <th colspan="4"></th>
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
  var sumAllAmount = "";
  var currencyUnit = '<?php echo my_get_currency_unit(); ?>';
  var lastSearchParams = {
    start_date: '<?php echo my_add_date(-29); ?>',
    end_date: '<?php echo date('Y-m-d'); ?>'
  };
  $(document).ready(function () {
    $('#filter_daterange').daterangepicker(custom_daterangepicker_option1, function (start, end, label) {
      $("#filter_daterange span").text(start.format('MMM DD, YY') + " - " + end.format('MMM DD, YY'));

      lastSearchParams.start_date = start.format('YYYY-MM-DD');
      lastSearchParams.end_date = end.format('YYYY-MM-DD');

      reloadTable(true);
    });
    
    $("#btnExcelExport").removeClass("btn-primary").addClass("btn-dark");

    $dataTable = $('#dataTable').DataTable({
      language: {
        url: "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>",
        searchPlaceholder: "<?php ___("Search by Product"); ?>"
      },
      columns: [
        {data: "index"},
        {data: "registered"},
        {data: "payment_method"},
        {data: "amount"},
        {data: "company"},
        {data: "category"},
        {data: "product"},
        {data: "actions"},
      ],
      order: [[1, "desc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("payments/expenses/ajax_find"); ?>",
        type: "POST",
        data: function (d) {
          lastSearchParams = $.extend(lastSearchParams, d, {
            company_id: document.frmFilter.company_id.value,
            category_id: document.frmFilter.category_id.value,
            payment_account: document.frmFilter.payment_account.value,
            payment_method: document.frmFilter.payment_method.value
          });

          return lastSearchParams;
        },
        dataFilter: function (response) {
          var data = $.parseJSON(response);
          sumAllAmount = data.sumAllAmount;
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

        $(row).dblclick(function () {
          location.href = '<?php echo base_url('payments/expenses/edit'); ?>/' + $(this).attr('id');
        });
      },
      footerCallback: function (row, data, start, end, display) {
        if (data.length == 0) {
        } else {
          $("#btnExcelExport").removeClass("btn-dark").addClass("btn-primary");
        }

        var api = this.api();
        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
          var amount = $(i).text();
          return typeof amount === 'string' ?
                  amount.replace(/[\$,]/g, '') * 1 :
                  typeof amount === 'number' ?
                  amount : 0;
        };

        // Total over all pages
//        total = api
//                .column(3)
//                .data()
//                .reduce(function (a, b) {
//                  console.log(a);
//                  return intVal(a) + intVal(b);
//                }, 0);

//        // Total over this page
//        pageTotal = api
//                .column(3, {page: 'current'})
//                .data()
//                .reduce(function (a, b) {
//                  return intVal(a) + intVal(b);
//                }, 0);

        var sumAmount = 0;
        for (i = 0; i < data.length; i++) {
          sumAmount += intVal(data[i].amount);
        }

        // Update footer
        $(api.column(1).footer()).html('$' + $.number(sumAmount, 2) + ' (All: ' + currencyUnit + sumAllAmount + ')');
      }
    });
    
    changeAccount($("#payment_account").val());
  });

  function reloadTable(resetPaging) {
    $("#btnExcelExport").removeClass("btn-primary").addClass("btn-dark");
    $dataTable.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_payment(id) {
    if (confirm("<?php ___("Are you sure delete selected payment history?"); ?>")) {
      $.get("<?php echo base_url('payments/expenses/ajax_delete') ?>/" + id, function () {
        reloadTable(false);
      })
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

    var url = "<?php echo base_url("payments/expenses/ajax_export_excel"); ?>";
    url += "?company_id=" + lastSearchParams.company_id;
    url += "&category_id=" + lastSearchParams.category_id;
    url += "&payment_account=" + lastSearchParams.payment_account;
    url += "&payment_method=" + lastSearchParams.payment_method;
    url += "&start_date=" + lastSearchParams.start_date;
    url += "&end_date=" + lastSearchParams.end_date;
    url += "&search=" + encodeURIComponent(lastSearchParams.search.value);

    location.href = url;
  }
</script>