<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$end_date = date('M d, y');
$start_date = my_add_date(-29, false, 'M d, y');
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Income vs Expenses"); ?></h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="<?php echo base_url("payments/income/add"); ?>"><i class="fa fa-indent"></i> <?php ___("New Income"); ?></a></li>
              <li><a href="<?php echo base_url("payments/expenses/add"); ?>"><i class="fa fa-outdent"></i> <?php ___("New Expenses"); ?></a></li>
            </ul>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>

      <div class="x_content">
        <div class="row">
          <div class="col-md-3 col-sm-5 col-xs-12">
            <?php
            $query = "select `type`, sum(amount) as sum_amount from payment_histories group by `type`";

            $amounts = $this->db->query($query)->result();

            $income_amount = 0;
            $expenses_amount = 0;
            foreach ($amounts as $amount) {
              if ($amount->type == PAYMENT_TYPE_INCOME) {
                $income_amount = $amount->sum_amount;
              } elseif ($amount->type == PAYMENT_TYPE_EXPENSES) {
                $expenses_amount = $amount->sum_amount;
              }
            }
            ?>

            <div class="tile-stats">
              <div class="icon"><i class="fa fa-money"></i></div>
              
              <div class="count blue"><?php echo my_show_amount($income_amount - $expenses_amount); ?></div>
              
              <h3><?php ___("Today Cash"); ?></h3>
              <div class="row">
                <div class="col-xs-6">
                  <p>
                    <a class="green" href="<?php echo base_url("payments/income"); ?>" data-toggle="tooltip" title="<?php ___("Total Income"); ?>">
                      <i class="fa fa-sort-asc"></i> <?php echo my_show_amount($income_amount); ?>
                    </a>
                  </p>
                </div>
                <div class="col-xs-6">
                  <p>
                    <a class="red" href="<?php echo base_url("payments/expenses"); ?>" data-toggle="tooltip" title="<?php ___("Total Expenses"); ?>">
                      <i class="fa fa-sort-desc"></i> <?php echo my_show_amount($expenses_amount); ?>
                    </a>
                  </p>
                </div>
              </div>
            </div>

            <div>
              <div id="today_income_vs_expenses" style="width: 200px; height: 200px; margin: 0 auto;"></div>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="col-md-9 col-sm-7 col-xs-12">
            <div class="row">
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div id="invome_vs_expenses_rangedate" class="daterangepicker-input" style="width: 200px;">
                  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                  <span><?php echo $start_date; ?> - <?php echo $end_date; ?></span> <b class="caret"></b>
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-6" id="income-progress">
                <p style="margin-bottom: 2px !important;"><?php ___("Income"); ?>: <span class="amount"></span></p>
                <div class="progress progress_sm" style="margin-bottom: 5px !important;">
                  <div class="progress-bar bg-green"></div>
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-6" id="expenses-progress">
                <p style="margin-bottom: 2px !important;"><?php ___("Expenses"); ?>: <span class="amount"></span></p>
                <div class="progress progress_sm" style="margin-bottom: 5px !important;">
                  <div class="progress-bar bg-red"></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="row">
              <div id="chart_income_vs_expenses" style="width:100%; height:280px;"></div>
            </div>
          </div>
        </div>

        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
<br />


<script>
  var total_income = <?php echo $income_amount; ?>;
  var total_expenses = <?php echo $expenses_amount; ?>;

  $(function () {
    $('#invome_vs_expenses_rangedate').daterangepicker(custom_daterangepicker_option1, function (start, end, label) {
      $("#invome_vs_expenses_rangedate span").text(start.format('MMM DD, YY') + " - " + end.format('MMM DD, YY'));

      var start_date = start.format('YYYY-MM-DD');
      var end_date = end.format('YYYY-MM-DD');

      draw_chart_for_income_vs_expenses(start_date, end_date);
    });

    Morris.Donut({
      element: 'today_income_vs_expenses',
      data: [
        {label: 'Income', value: <?php echo $income_amount; ?>},
        {label: 'Expenses', value: <?php echo $expenses_amount; ?>}
      ],
      colors: ['#1ABB9C', '#E74C3C'],
      formatter: function (y) {
        return my_js_options.current_unit + $.number(y, 2);
      },
      resize: true
    });

    draw_chart_for_income_vs_expenses('<?php echo my_add_date(-29); ?>', '<?php echo date('Y-m-d'); ?>');
  })

  function draw_chart_for_income_vs_expenses(start, end) {
    $.post("<?php echo base_url("reports/ajax_get_income_vs_expensens"); ?>", {
      start_date: start,
      end_date: end
    }, function (chart_data) {
      $("#chart_income_vs_expenses").empty();

      Morris.Bar({
        element: 'chart_income_vs_expenses',
        data: chart_data.chart,
        xkey: 'date',
        barColors: ['#1ABB9C', '#E74C3C'],
        ykeys: ['income', 'expenses'],
        labels: ['<?php ___("Income"); ?>', '<?php ___("Expenses"); ?>'],
        hideHover: 'auto',
        xLabelAngle: 30,
        resize: true,
        /*xLabelFormat: function (x) {
         return $.number(x, 2);
         },*/
        yLabelFormat: function (y) {
          return my_js_options.current_unit + $.number(y, 2);
        }
      });

      $("#income-progress").find("span.amount").text(my_js_options.current_unit + $.number(chart_data.sum_income));
      if (total_income > 0) {
        $("#income-progress").find("div.progress-bar").attr("data-transitiongoal", chart_data.sum_income * 100 / total_income).progressbar();
      }

      $("#expenses-progress").find("span.amount").text(my_js_options.current_unit + $.number(chart_data.sum_expenses));
      if (total_expenses > 0) {
        $("#expenses-progress").find("div.progress-bar").attr("data-transitiongoal", chart_data.sum_expenses * 100 / total_expenses).progressbar();
      }
    }, 'json');
  }
</script>