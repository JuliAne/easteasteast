<div class="singlesite">

	<?php include_once('header.tpl.php'); ?>

  <?php if(!empty($messages) && !strpos($messages, 'Warning') && !strpos($messages, 'session')) : ?>
	<div id="alert" class="drupal-error">
	  <?php print $messages; ?>
	  <a href="#" class="close" title="Schliessen">x</a>
	</div>
  <?php endif; ?>

	<?php print render($page['content']); ?>
	<?php include_once('footer.tpl.php'); ?>

</div>
