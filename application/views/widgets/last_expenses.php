<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$CI = &get_instance();
$CI->load->model("payment_method_m");
$CI->load->model("payment_history_m");

$CI->payment_methods = $CI->payment_method_m->get_methods();

$order = array(array(
        "column" => 1,
        "dir" => "desc"
    ));

$expenses_types = my_get_expenses_types();
$expenses = array();
$result = $CI->payment_history_m->find(PAYMENT_TYPE_EXPENSES, "all", "all", "all", "", "all", false, false, "all", $order, 0, 20);
if ($result['count'] > 0) {
  foreach ($result['data'] as $row) {
    $payment_method = "";
    if (isset($CI->payment_methods[$row->payment_method_id])) {
      $payment_method = $CI->payment_methods[$row->payment_method_id];
    }

    $amount_text = my_show_amount($row->amount, false);
    if ($row->amount != $row->before_amount) {
      $amount_text .= '(<strike>' . my_show_amount($row->before_amount, false) . '</strike>)';
    }

    if ($row->status == PAYMENT_STATUS_PENDING) {
      $amount_text = '<span class="datatable-number red" title="Pending"><i>' . $amount_text . '</i></span>';
    } else {
      $amount_text = '<span class="datatable-number">' . $amount_text . '</span>';
    }

    $payment = array(
        "id" => $row->id,
        "reason" => "[" . $expenses_types[$row->reason_type] . "] - " . $row->reason_desc,
        "registered" => my_formart_date($row->pay_date) . " <small>" . $row->pay_time . '</small>',
        "amount" => $amount_text,
        "payment_method" => $payment_method
    );
    $expenses[] = $payment;
  }
}
?>

<div class="x_panel">
  <div class="x_title">
    <h2><?php ___("Last Expenses"); ?></h2>
    <ul class="nav navbar-right panel_toolbox">
      <li><a href="<?php echo base_url("payments/expenses"); ?>" title="<?php ___("More Expenses"); ?>" data-toggle="tooltip"><i class="fa fa-outdent"></i></a></li>
      <li><a href="<?php echo base_url("payments/expenses/add"); ?>" title="<?php ___("New Expenses"); ?>" data-toggle="tooltip"><i class="fa fa-plus-circle"></i></a></li>
      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
    </ul>
    <div class="clearfix"></div>
  </div>
  <div class="x_content">
    <?php if (empty($expenses)): ?>
      <p><?php ___("Empty expenses histories."); ?></p>
    <?php else: ?>
      <table class="table table-striped projects">
        <thead>
          <tr>
            <th><?php ___("Reason"); ?></th>
            <th><?php ___("Amount"); ?></th>
            <th><?php ___("Payment"); ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($expenses as $payment): ?>
            <tr>
              <td>
                <?php echo $payment['reason']; ?>
              </td>
              <td>
                <?php echo $payment['amount']; ?>
              </td>
              <td>
                in <?php echo $payment['registered']; ?>
                <br />
                by <?php echo $payment['payment_method']; ?>
              </td>
              <td>
                <a href="<?php echo base_url("payments/expenses/edit/" . $payment['id']); ?>" class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> <?php ___("View"); ?> </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>