<?php include_once('header.tpl.php'); ?>

<?php if (!strpos(current_path(), 'Akteurprofil') !== FALSE) : ?>
<div class="row">
 <?php print render($page['content']); ?>
</div>
<?php else : ?>
 <?= print render($page['content']); ?>
<?php endif; ?>

<?php include_once('footer.tpl.php'); ?>
