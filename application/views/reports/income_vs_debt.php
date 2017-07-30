<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$end_date = date('M d, y');
$start_date = my_add_date(-29, false, 'M d, y');
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Income vs Debt"); ?><small><?php ___("Paid and Pending payments"); ?></small></h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="<?php echo base_url("payments/income/add"); ?>"><i class="fa fa-indent"></i> <?php ___("New Income"); ?></a></li>
            </ul>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>

      <div class="x_content">
        <div class="row">
          <div class="col-md-3 col-sm-5 col-xs-12">
            <?php
            $query = "select `status`, sum(amount) as sum_amount from payment_histories where `type`=" . PAYMENT_TYPE_INCOME . " group by `status`";

            $amounts = $this->db->query($query)->result();

            $total_incom = 0;
            $paid_amount = 0;
            $pendding_amount = 0;
            foreach ($amounts as $amount) {
              if ($amount->status == PAYMENT_STATUS_PAID) {
                $paid_amount = $amount->sum_amount;
              } elseif ($amount->status == PAYMENT_STATUS_PENDDING) {
                $pendding_amount = $amount->sum_amount;
              }
            }
            $total_incom = $paid_amount + $pendding_amount;
            ?>

            <div class="tile-stats">
              <div class="icon"><i class="fa fa-indent"></i></div>

              <div class="count blue"><?php echo my_show_amount($total_incom); ?></div>

              <h3><?php ___("Total Income"); ?></h3>
              <div class="row">
                <div class="col-xs-6">
                  <p>
                    <a class="green" href="<?php echo base_url("payments/income/index/status/" . PAYMENT_STATUS_PAID); ?>" data-toggle="tooltip" title="<?php ___("Paid"); ?>">
                      <?php echo my_show_amount($paid_amount); ?>
                    </a>
                  </p>
                </div>
                <div class="col-xs-6">
                  <p>
                    <a class="red" href="<?php echo base_url("payments/income/index/status/" . PAYMENT_STATUS_PENDDING); ?>" data-toggle="tooltip" title="<?php ___("Pendding"); ?>">
                      <?php echo my_show_amount($pendding_amount); ?>
                    </a>
                  </p>
                </div>
              </div>
            </div>

            <div>
              <div id="today_income_vs_debt" style="height: 200px;"></div>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="col-md-9 col-sm-7 col-xs-12">
            <div class="row">
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div id="invome_vs_debt_rangedate" class="daterangepicker-input" style="width: 200px;">
                  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                  <span><?php echo $start_date; ?> - <?php echo $end_date; ?></span> <b class="caret"></b>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="row">
              <div id="chart_income_vs_debt" style="width:100%; height:360px;"></div>
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
  var total_paid = <?php echo $paid_amount; ?>;
  var total_pendding = <?php echo $pendding_amount; ?>;
  var echartPieDebt, echartLineDebt;

  $(function () {
    echartPieDebt = echarts.init(document.getElementById('today_income_vs_debt'), {
      color: ['#26B99A', '#E74C3C']
    });

    echartPieDebt.setOption({
      tooltip: {
        trigger: 'item',
        formatter: function (params) {
          //console.log(params);
          var title = params.name + ": <small>" + params.percent + "%</small>" + '<br/>' + my_js_options.current_unit + $.number(params.value, 2);
          return title;
        }//"{a} <br/>{b} : " + my_js_options.current_unit + "{c} ({d}%)"
      },
      legend: {
        show: false,
        x: 'center',
        y: 'bottom',
        data: ['<?php ___("Paid") ?>', '<?php ___("Pendding") ?>']
      },
      toolbox: {
        show: false
      },
      calculable: true,
      series: [{
          name: '<?php ___("Total: %s%s", my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string"), number_format($total_incom)); ?>',
          type: 'pie',
          radius: '55%',
          center: ['50%', '50%'],
          data: [{
              value: total_paid,
              name: '<?php ___("Paid") ?>'
            }, {
              value: total_pendding,
              name: '<?php ___("Pendding") ?>'
            }]
        }]
    });

    $('#invome_vs_debt_rangedate').daterangepicker(custom_daterangepicker_option1, function (start, end, label) {
      $("#invome_vs_debt_rangedate span").text(start.format('MMM DD, YY') + " - " + end.format('MMM DD, YY'));

      var start_date = start.format('YYYY-MM-DD');
      var end_date = end.format('YYYY-MM-DD');

      draw_chart_for_income_vs_debt(start_date, end_date);
    });

    draw_chart_for_income_vs_debt('<?php echo my_add_date(-29); ?>', '<?php echo date('Y-m-d'); ?>');

    $(window).on('resize', function () {
      if (echartPieDebt != null && echartPieDebt != undefined) {
        echartPieDebt.resize();
      }
      
      if (echartLineDebt != null && echartLineDebt != undefined) {
        echartLineDebt.resize();
      }
    });
  })

  function draw_chart_for_income_vs_debt(start, end) {
    $.post("<?php echo base_url("reports/ajax_get_income_vs_debt"); ?>", {
      start_date: start,
      end_date: end
    }, function (chart_data) {
      $("#chart_income_vs_debt").empty();

      echartLineDebt = echarts.init(document.getElementById('chart_income_vs_debt'), {
        color: ['#3498DB', '#26B99A', '#E74C3C']
      });

      echartLineDebt.setOption({
        title: {
          show: false
        },
        tooltip: {
          trigger: 'axis'
        },
        legend: {
          x: 100,
          y: 20,
          data: ['<?php ___("Income") ?>', '<?php ___("Paid") ?>', '<?php ___("Pendding") ?>']
        },
        toolbox: {
          show: true,
          feature: {
            magicType: {
              show: true,
              title: {
                line: 'Line',
                bar: 'Bar',
                stack: 'Stack',
                tiled: 'Tiled'
              },
              type: ['line', 'bar', 'stack', 'tiled']
            }
          }
        },
        calculable: true,
        xAxis: [{
            type: 'category',
            boundaryGap: true,
            data: chart_data.dates
          }],
        yAxis: [{
            type: 'value'
          }],
        series: [{
            name: '<?php ___("Income") ?>',
            type: 'line',
            smooth: true,
            itemStyle: {
              normal: {
                areaStyle: {
                  type: 'default'
                }
              }
            },
            data: chart_data.income
          }, {
            name: '<?php ___("Paid") ?>',
            type: 'line',
            smooth: true,
            itemStyle: {
              normal: {
                areaStyle: {
                  type: 'default'
                }
              }
            },
            data: chart_data.paid
          }, {
            name: '<?php ___("Pendding") ?>',
            type: 'line',
            smooth: true,
            itemStyle: {
              normal: {
                areaStyle: {
                  type: 'default'
                }
              }
            },
            data: chart_data.pendding
          }]
      });
    }, 'json');
  }

</script>