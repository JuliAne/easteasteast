<?php include_once('header.tpl.php'); ?>

<?php if (strpos(current_path(), 'Akteurprofil') !== FALSE) echo 'Akteurprofil'; ?>

<?php print render($page['content']); ?>

<?php include_once('footer.tpl.php'); ?>
