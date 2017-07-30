<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$today_educations = array();

$CI = &get_instance();
$query = "select c.*, p.`name` as product_name";
$query.= " from education_classes c join education_products p on c.product_id=p.`id`";
$query.= " where (c.`status` = 'plan' or c.`status` = 'running')";
$query.= " and c.start_datetime <= '" . date('Y-m-d') . " 23:59'";
$query.= " order by c.start_datetime";

$_classes = $CI->db->query($query)->result();
$unit = my_get_currency_unit();
foreach ($_classes as $_class) {
  if ($_class->registration_fee * 1 > 0) {
    $query = "select distinct s.*";
    $query.= " from education_class_students cs join students s on cs.student_id=s.`id`";
    $query.= " where cs.`id` not in (select class_student_id from education_payments where paid_step_id=0 and class_id=" . $_class->id . ")";
    $query.= " and cs.class_id=" . $_class->id;
    $query.= " order by s.first_name";

    $_students = $CI->db->query($query)->result();
    if (count($_students) > 0) {
      if (!isset($today_educations[$_class->id])) {
        $today_educations[$_class->id] = array(
            "class_id" => $_class->id,
            "class_name" => $_class->product_name,
            "start_datetime" => $_class->start_datetime,
            "status" => $_class->status,
            "students" => array()
        );
      }

      foreach ($_students as $_student) {
        $today_educations[$_class->id]['students'][] = array(
            "name" => $_student->first_name . " " . $_student->last_name,
            "email" => $_student->email,
            "description" => __("need to pay fee, %s%s", $unit, number_format($_class->registration_fee, 2))
        );
      }
    }
  }

  $query = "select * from education_class_steps where class_id=" . $_class->id . " and due_date <= '" . date('Y-m-d') . "' order by due_date";
  $_steps = $CI->db->query($query)->result();
  foreach ($_steps as $_step) {
    $query = "select distinct s.*";
    $query.= " from education_class_students cs join students s on cs.student_id=s.`id`";
    $query.= " where cs.`id` not in (select class_student_id from education_payments where paid_step_id=" . $_step->step . " and class_id=" . $_class->id . ")";
    $query.= " and cs.class_id=" . $_class->id;
    $query.= " order by s.first_name";

    $_students = $CI->db->query($query)->result();
    if (count($_students) > 0) {
      if (!isset($today_educations[$_class->id])) {
        $today_educations[$_class->id] = array(
            "class_id" => $_class->id,
            "class_name" => $_class->product_name,
            "start_datetime" => $_class->start_datetime,
            "status" => $_class->status,
            "students" => array()
        );
      }

      foreach ($_students as $_student) {
        $today_educations[$_class->id]['students'][] = array(
            "name" => $_student->first_name . " " . $_student->last_name,
            "email" => $_student->email,
            "description" => __("need to pay %s%s at %s", $unit, number_format($_step->cost, 2), my_formart_date($_step->due_date))
        );
      }
    }
  }
}
?>

<style>
  .timeline .class-plan h2.title:before {
    border-color: #1ABB9C;
    background: #1ABB9C;
  }
  .timeline .class-running h2.title:before {
    border-color: #349ADB;
    background: #349ADB;
  }
</style>

<div class="x_panel">
  <div class="x_title">
    <h2><?php echo my_formart_date(); ?></h2>
    <ul class="nav navbar-right panel_toolbox">
      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
    </ul>
    <div class="clearfix"></div>
  </div>
  <div class="x_content">
    <?php if (empty($today_educations)): ?>
      <p><?php ___("There is no education to pay today."); ?></p>
    <?php else: ?>
      <ul class="list-unstyled timeline widget">
        <?php foreach ($today_educations as $class_id => $class_info): ?>
          <li class="class-<?php echo $class_info['status']; ?>">
            <div class="block">
              <div class="block_content">
                <h2 class="title">
                  <a href="<?php echo base_url("education/classes/edit/" . $class_id); ?>"><?php echo $class_info["class_name"]; ?></a>
                </h2>
                <div class="byline">
                  <span><?php ___("Started in"); ?></span> <a><?php echo my_formart_date($class_info['start_datetime']); ?></a>
                </div>
                <?php foreach ($class_info['students'] as $_student): ?>
                  <p class="excerpt">
                    <a href="mailto:<?php echo $_student['email']; ?>"><?php echo $_student['name']; ?></a>
                    <?php echo $_student['description']; ?>
                  </p>
                <?php endforeach; ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?> 
      <?php endif; ?>
  </div>
</div>

