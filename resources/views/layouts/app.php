<?php
/**
 * CelesView — Base Layout (app.php)
 * 
 * This layout wraps all page content with the navbar and footer.
 * $content variable is injected by the Controller::view() method.
 * $title variable sets the page title.
 */


?>
<?php require_once base_path('resources/views/layouts/navbar.php'); ?>

<div id="topProgressBar" class="top-progress-bar"></div>

<?= $content ?? '' ?>

<?php require_once base_path('resources/views/layouts/footer.php'); ?>
