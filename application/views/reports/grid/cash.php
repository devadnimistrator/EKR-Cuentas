<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("reports"); ?>"><?php ___("Reports"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Grid"); ?> </small>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Cash"); ?> </small>
    </h3>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-5 col-sm-12">
    <div class="x_panel">
      <div class="row x_title">
        <div class="col-md-8 col-sm-8 col-xs-12">
          <div id="grid-rangedate" class="daterangepicker-input pull-left" style="width: 210px; margin-right: 5px;">
            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
            <span><?php echo $start_date; ?> - <?php echo $end_date; ?></span> <b class="caret"></b>
          </div>
          <select id="grid-account-type" class="form-control pull-left" style="width: 100px; display: inline-block; height: 32px;">
            <option value="all">- <?php ___("All"); ?> -</option>
            <?php foreach (my_get_payment_methods() as $id => $name): ?>
              <option value="<?php echo $id; ?>" <?php if ($account == $id) echo "selected"; ?>><?php echo $name; ?></option>
            <?php endforeach; ?>
          </select>
          <div class="clearfix"></div>
        </div>
        <ul class="nav navbar-right panel_toolbox">
          <li><button id="btnExcelExport" type="button" class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-file-excel-o"></i> <?php ___("Export"); ?></button></a>
        </ul>
        <div class="clearfix"></div>
      </div>

      <div class="x_content">
        <table id="report-cash" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th><?php ___("Date") ?>:</th>
              <th class="text-right"><?php ___("Income(%s)", my_get_currency_unit()) ?>:</th>
              <th class="text-right"><?php ___("Expenses(%s)", my_get_currency_unit()) ?>:</th>
              <th class="text-right"><?php ___("Remaining(%s)", my_get_currency_unit()) ?>:</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <td class="text-right">
                <?php ___("Before Remaining"); ?>:<br/>
                <strong id="before_remaining"></strong>
              </td>
              <td class="text-right">
                <?php ___("Sum Income"); ?>:<br/>
                <strong id="sum_income"></strong>
              </td>
              <td class="text-right">
                <?php ___("Sum Expenses"); ?>:<br/>
                <strong id="sum_expenses"></strong>
              </td>
              <td class="text-right">
                <?php ___("Last Remaining"); ?>:<br/>
                <strong id="sum_remaining"></strong>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-7 col-sm-12" id="detail_grid_table" style="display: none;">
    <div class="x_panel">
      <div class="x_title">						
        <h2 id="detail_grid_table_title"></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="detailTable" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Registered") ?>:</th>
              <th><?php ___("Payment") ?>:</th>
              <th><?php ___("Amount(%s)", my_get_currency_unit()) ?>:</th>
              <th class="nosort"><?php ___("Reason") ?>:</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  var gridStartDate = '<?php echo my_add_date(-29); ?>';
  var gridEndDate = '<?php echo date('Y-m-d'); ?>';

  $(function () {
    $('#grid-rangedate').daterangepicker(custom_daterangepicker_option1, function (start, end, label) {
      $("#grid-rangedate span").text(start.format('MMM DD, YY') + " - " + end.format('MMM DD, YY'));

      gridStartDate = start.format('YYYY-MM-DD');
      gridEndDate = end.format('YYYY-MM-DD');

      reloadTableReportCash();
    });

    $("#grid-account-type").change(function () {
      reloadTableReportCash();
    });
  })
</script>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
  var $dataTableReportCash;
  var $dataTableDetail;
  var sumAllAmount = "";
  var currencyUnit = '<?php echo my_get_currency_unit(); ?>';

  var lastSearchParams = {};

  $(document).ready(function () {
    $("#btnExcelExport").removeClass("btn-primary").addClass("btn-dark");

    var tableHeight = false;
    if ($(window).width() >= 768) {
      tableHeight = $(window).height() - 350;
    }

    $dataTableReportCash = $('#report-cash').DataTable({
      language: {
        url: "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>",
        searchPlaceholder: "<?php ___("Search by Reason"); ?>"
      },
      columns: [
        {data: "date"},
        {data: "income"},
        {data: "expenses"},
        {data: "remaining"}
      ],
      paging: false,
      ordering: false,
      info: false,
      bFilter: false,
      processing: true,
      serverSide: true,
      responsive: true,
      /*bScrollCollapse: tableHeight ? true : false,
       scrollY: tableHeight ? (tableHeight + 'px') : false,*/
      ajax: {
        url: "<?php echo base_url("reports/grid/ajax_get_cash"); ?>",
        type: "POST",
        data: function (d) {
          return {
            start_date: gridStartDate,
            end_date: gridEndDate,
            account: $("#grid-account-type").val()
          };
        },
        dataFilter: function (response) {
          var data = $.parseJSON(response);

          $("#before_remaining").text(my_js_options.current_unit + $.number(data.before_remaining, 2));
          $("#sum_income").text(my_js_options.current_unit + $.number(data.sum_income, 2));
          $("#sum_expenses").text(my_js_options.current_unit + $.number(data.sum_expenses, 2));
          $("#sum_remaining").text(my_js_options.current_unit + $.number(data.sum_remaining, 2));

          if (data.data.length > 0) {
            $("#btnExcelExport").removeClass("btn-dark").addClass("btn-primary");
          }
          //sumAllAmount = data.sumAllAmount;
          return response;
        }
      }
    });

    $dataTableDetail = $('#detailTable').DataTable({
      language: {
        url: "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>",
        searchPlaceholder: "<?php ___("Search by Reason"); ?>"
      },
      columns: [
        {data: "index"},
        {data: "registered"},
        {data: "payment_method"},
        {data: "amount_text"},
        {data: "reason"}
      ],
      order: [[1, "desc"]],
      processing: true,
      serverSide: true,
      responsive: true,
      ajax: {
        url: "<?php echo base_url("payments/income/ajax_find"); ?>",
        type: "POST",
        data: function (d) {
          lastSearchParams = $.extend(lastSearchParams, d);

          return lastSearchParams;
        }
      },
      aoColumnDefs: [{
          'bSortable': false,
          'aTargets': ['nosort']
        }]
    });
  });

  function reloadTableReportCash(resetPaging) {
    $("#detail_grid_table").hide();
    $("#btnExcelExport").removeClass("btn-primary").addClass("btn-dark");
    $dataTableReportCash.ajax.reload(function () {
    }, resetPaging);
  }

  function exportExcel() {
    if ($("#btnExcelExport").hasClass("btn-dark")) {
      return;
    }

    var url = "<?php echo base_url("reports/grid/ajax_export_cash"); ?>";
    url += "?start_date=" + gridStartDate;
    url += "&end_date=" + gridEndDate;
    url += "&account=" + $("#grid-account-type").val();

    location.href = url;
  }

  function showDetailCash(date, type, title) {
    $("#detail_grid_table_title").text(title + ", " + date);
    $("#detail_grid_table").show();

    var url = ""
    if (type == <?php echo PAYMENT_TYPE_INCOME; ?>) {
      url = "<?php echo base_url("payments/income/ajax_find"); ?>";
      lastSearchParams = {
        start_date: '0000-00-00',
        end_date: '0000-00-00',
        income_type: 'all',
        category_id: 'all',
        payment_account: 'all',
        payment_method: 'all',
        payment_status: 'all'
      };
    } else {
      url = "<?php echo base_url("payments/expenses/ajax_find"); ?>";
      lastSearchParams = {
        start_date: '0000-00-00',
        end_date: '0000-00-00',
        expenses_type: 'all',
        company_id: 'all',
        category_id: 'all',
        payment_account: 'all',
        payment_method: 'all'
      };
    }

    if (date.length == 7) {
      lastSearchParams.start_date = date + '-01';
      lastSearchParams.end_date = date + '-31';
    } else {
      lastSearchParams.start_date = date;
      lastSearchParams.end_date = date;
    }
    $dataTableDetail.ajax.url(url).load();
    
    location.href = '#detail_grid_table';
  }
</script>