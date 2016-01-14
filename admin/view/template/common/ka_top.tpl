<?php
/*
  Project: Ka Extensions
  Author : karapuz <support@ka-station.com>

  Version: 2 ($Revision: 34 $)
*/

?>
<?php if (!empty($breadcrumbs)) { $breadcrumb_started = false; ?>
  <!--<ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php if (!empty($breadcrumb_started)) { echo ' :: '; } $breadcrumb_started = true;  ?>
      <?php if (!empty($breadcrumb['href'])) { ?>
        <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } else { ?>
        <?php echo $breadcrumb['text']; ?>
      <?php } ?>
    <?php } ?>
  </ul>-->
  <ul class="breadcrumb"> 
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
<?php } ?>

<?php if (!empty($ka_top_messages)) { ?>
  <?php foreach ($ka_top_messages as $ka_top_message) { ?>
    <?php if ($ka_top_message['type'] == 'E') { ?>
    	<div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> <?php echo $ka_top_message['content']; ?>
      	<button type="button" class="close" data-dismiss="alert">&times;</button>
    	</div>
    <?php } else { ?>
    <div class="alert alert-success">
      <i class="fa fa-check-circle"></i> <?php echo $ka_top_message['content']; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
  <?php } ?>
<?php } ?>