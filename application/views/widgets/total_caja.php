<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$query = "select `type`, sum(amount) as sum_amount from payment_histories where payment_method_id"
    . " in (select `id` from payment_methods where `type`='" . PAYMENT_METHOD_CAJA . "')"
    . " group by `type`";

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

if (!isset($show_chart))
  $show_chart = true;
?>

<div class="tile-stats">
  <div class="icon"><i class="fa fa-money"></i></div>
  <div class="count blue"><?php echo my_show_amount($income_amount - $expenses_amount); ?></div>

  <h3><?php ___("Caja Chica"); ?></h3>
  <div class="row">
    <div class="col-xs-6">
      <p>
        <a class="green" href="<?php echo base_url("payments/income/index/account/" . PAYMENT_METHOD_BANK); ?>" data-toggle="tooltip" title="<?php ___("Total Income"); ?>">
          <i class="fa fa-sort-asc"></i> <?php echo my_show_amount($income_amount); ?>
        </a>
      </p>
    </div>
    <div class="col-xs-6">
      <p>
        <a class="red" href="<?php echo base_url("payments/expenses/index/account/" . PAYMENT_METHOD_BANK); ?>" data-toggle="tooltip" title="<?php ___("Total Expenses"); ?>">
          <i class="fa fa-sort-desc"></i> <?php echo my_show_amount($expenses_amount); ?>
        </a>
      </p>
    </div>
  </div>

  <?php if ($show_chart): ?>
    <div class="ln_solid"></div>

    <div id="total_caja_pie_chart" style="height: 300px;"></div>

    <script>
      $(function () {
        var total_caja_pie_chart = echarts.init(document.getElementById('total_caja_pie_chart'), {
          color: ['#26B99A', '#E74C3C']
        });
        total_caja_pie_chart.setOption({
          tooltip: {
            trigger: 'item',
            formatter: function (params) {
              var title = params.name + ": <small>" + params.percent + "%</small>" + '<br/>' + my_js_options.current_unit + $.number(params.value, 2);
              return title;
            }
          },
          legend: {
            show: false,
            x: 'center',
            y: 'bottom',
            data: ['<?php ___("Income") ?>', '<?php ___("Expenses") ?>']
          },
          toolbox: {
            show: true,
            feature: {
              magicType: {
                show: true,
                type: ['pie', 'funnel']
              },
              saveAsImage: {
                show: true,
                title: "<?php ___("Save Image"); ?>"
              }
            }
          },
          calculable: true,
          series: [{
              type: 'pie',
              radius: '55%',
              center: ['50%', '50%'],
              data: [{
                  value: <?php echo $income_amount; ?>,
                  name: '<?php ___("Income") ?>'
                }, {
                  value: <?php echo $expenses_amount; ?>,
                  name: '<?php ___("Expenses") ?>'
                }]
            }]
        });
      });

    </script>
  <?php endif; ?>
  <div class="clearfix"></div>
</div>