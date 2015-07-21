<?php if(user_is_logged_in()) : ?>
  <a class="button secondary" href="<?= base_path(); ?>?q=Akteurformular">+ Akteur hinzufügen</a><br />
<?php endif; ?>

<?= $itemsCount; ?>

<?php foreach($resultakteure as $row): ?>
  <a href="<?= base_path().'?q=Akteurprofil/'.$row->AID.'">'.$row->name.'</a><br>'; ?>
<?php endif; ?>

<div class="divider"></div>
  <ul class="pagination">
  <li class="arrow unavailable"><a href="">&laquo;</a></li>
  <li class="current"><a href="">1</a></li>
  <li><a href="">2</a></li>
  <li><a href="">3</a></li>
  <li><a href="">4</a></li>
  <li class="unavailable"><a href="">&hellip;</a></li>
  <li><a href="">12</a></li>
  <li><a href="">13</a></li>
  <li class="arrow"><a href="">&raquo;</a></li>
</ul>
