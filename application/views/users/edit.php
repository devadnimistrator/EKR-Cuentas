<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("users"); ?>"><?php ___("Cashiers"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?></small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("users/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <?php
    $user_m->show_errors();
    $userinfo_m->show_errors();
    $userinfo_m->show_msgs();
    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "editUser",
    "autocomplete" => false
);

$user_m->form_create($formConfig);

$userinfo_m->form_create($formConfig);
$userinfo_m->bs_form->form_start(TRUE);
?>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Account Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $user_m->form_add_element("username");
        $user_m->form_add_element("password");
        $user_m->bs_form->add_element("status", BSFORM_SELECT, $user_m->status, array("label" => __("Status"), "options" => __($this->config->item('data_status'))));
        $user_m->bs_form->form_elements(TRUE);
        ?>

        <div class="ln_solid"></div>

        <?php
        $userinfo_m->bs_form->form_buttons(TRUE);
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>

  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Profile Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $userinfo_m->form_add_element("fullname");
        $userinfo_m->form_add_element("email");
        $userinfo_m->form_add_element("phone");
        $userinfo_m->bs_form->form_elements(TRUE);
        ?>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>

<?php
$userinfo_m->bs_form->form_end(TRUE);
