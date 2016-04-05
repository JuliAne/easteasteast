<?php
 /* Inidivdual page for displaying the latest journal-posts */ 
?>
<div id="journalPage" class="singlesite">

	<?php include_once('header.tpl.php'); ?>

	<div id="contentRow" class="row"><?php print render($page['content']); ?><?php print render($page['journal_latest_posts']); ?>
</div>
	<?php include_once('footer.tpl.php'); ?>

</div>
