<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>

<li class="<?php if ($page_slug == 'payments') echo "active" ?>">
  <a href="<?php echo base_url("payments"); ?>"><i class="fa fa-dollar"></i> <?php ___("Payments"); ?><span class="fa fa-chevron-down"></span></a>
  <ul class="nav child_menu" <?php if ($page_slug == 'payments') echo 'style="display: block;"' ?>>
    <li class="<?php if ($page_sub_slug == 'income') echo "active" ?>"><a href="<?php echo site_url("payments/income"); ?>"><i class="fa fa-indent"></i><?php ___("Income"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'expenses') echo "active" ?>"><a href="<?php echo site_url("payments/expenses"); ?>"><i class="fa fa-outdent"></i><?php ___("Expenses"); ?></a></li>
  </ul>
</li>

<li class="<?php if ($page_slug == 'education') echo "active" ?>">
  <a href="<?php echo base_url('education/calendar'); ?>"><i class="glyphicon glyphicon-education"></i> <?php ___("Education"); ?><span class="fa fa-chevron-down"></span></a>
  <ul class="nav child_menu" <?php if ($page_slug == 'education') echo 'style="display: block;"' ?>>
    <li class="<?php if ($page_sub_slug == 'calendar') echo "active" ?>"><a href="<?php echo site_url("education/calendar"); ?>"><i class="fa fa-calendar"></i><?php ___("Calendar"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'classes') echo "active" ?>"><a href="<?php echo site_url("education/classes"); ?>"><i class="fa fa-slideshare"></i><?php ___("Scheduled"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'products') echo "active" ?>"><a href="<?php echo site_url("education/products"); ?>"><i class="fa fa-product-hunt"></i><?php ___("Programs"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'categories') echo "active" ?>"><a href="<?php echo site_url("education/categories"); ?>"><i class="fa fa-tags"></i><?php ___("Categories"); ?></a></li>
    <li><a href="<?php echo site_url("payments/income/index/type/" . PAYMENT_REASON_TYPE_EDUCATION); ?>"><i class="fa fa-credit-card"></i> <?php ___("Payments"); ?></a></li>
  </ul>
</li>

<li class="<?php if ($page_slug == 'tangible') echo "active" ?>">
  <a href="<?php echo base_url('tangible/products'); ?>"><i class="fa fa-book"></i> <?php ___("Tangible"); ?><span class="fa fa-chevron-down"></span></a>
  <ul class="nav child_menu" <?php if ($page_slug == 'tangible') echo 'style="display: block;"' ?>>
    <li class="<?php if ($page_sub_slug == 'products') echo "active" ?>"><a href="<?php echo site_url("tangible/products"); ?>"><i class="fa fa-product-hunt"></i><?php ___("Products"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'categories') echo "active" ?>"><a href="<?php echo site_url("tangible/categories"); ?>"><i class="fa fa-tags"></i><?php ___("Categories"); ?></a></li>
    <li><a href="<?php echo site_url("payments/income/index/type/" . PAYMENT_REASON_TYPE_TANGIBLE); ?>"><i class="fa fa-credit-card"></i> <?php ___("Payments"); ?></a></li>
  </ul>
</li>

<li class="<?php if ($page_slug == 'expenses') echo "active" ?>">
  <a href="<?php echo base_url('expenses/products'); ?>"><i class="fa fa-outdent"></i> <?php ___("Expenses"); ?><span class="fa fa-chevron-down"></span></a>
  <ul class="nav child_menu" <?php if ($page_slug == 'expenses') echo 'style="display: block;"' ?>>
    <li class="<?php if ($page_sub_slug == 'products') echo "active" ?>"><a href="<?php echo site_url("expenses/products"); ?>"><i class="fa fa-product-hunt"></i><?php ___("Products"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'categories') echo "active" ?>"><a href="<?php echo site_url("expenses/categories"); ?>"><i class="fa fa-tags"></i><?php ___("Categories"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'companies') echo "active" ?>"><a href="<?php echo site_url("expenses/companies"); ?>"><i class="fa fa-building"></i><?php ___("Companies"); ?></a></li>
    <li><a href="<?php echo site_url("payments/expenses/index/type/" . PAYMENT_REASON_TYPE_EXPENSES); ?>"><i class="fa fa-credit-card"></i> <?php ___("Payments"); ?></a></li>
  </ul>
</li>

<li class="<?php if ($page_slug == 'students') echo "active" ?>">
  <a href="<?php echo base_url('students'); ?>"><i class="fa fa-child"></i> <?php ___("Students"); ?><span class="fa fa-chevron-down"></span></a>
  <ul class="nav child_menu" <?php if ($page_slug == 'students') echo 'style="display: block;"' ?>>
    <li class="<?php if ($page_sub_slug == 'add') echo "active" ?>"><a href="<?php echo site_url("students/add"); ?>"><i class="fa fa-plus"></i><?php ___("Add New"); ?></a></li>
    <li class="<?php if ($page_sub_slug == 'index') echo "active" ?>"><a href="<?php echo site_url("students"); ?>"><i class="fa fa-bars"></i><?php ___("All Students"); ?></a></li>
  </ul>
</li>