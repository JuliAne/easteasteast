<?php
 /* Inidivdual page for displaying the latest journal-posts */
?>
<style type="text/css">
.jEntryPrev { border: 1px solid #e9e9e9; }
.jEntry .jFooter .jAside span { color: grey; }
.jEntry #share a { background-color:#2199E8; }
.jEntry #share a:hover { background-color: #007095 !important; }
</style>
<div id="journalPage" class="singlesite">

	<?php include_once('header.tpl.php'); ?>

	<div id="contentRow" class="row">
	<?php print render($page['content']); ?><?php print render($page['journal_latest_posts']); ?>
  </div>
	<?php include_once('footer.tpl.php'); ?>

</div>
