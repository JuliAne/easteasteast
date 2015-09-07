<?php if (!empty($_SESSION['sysmsg'])) : ?>
<div id="alert">
  <?php foreach ($_SESSION['sysmsg'] as $msg): ?>
    <?= $msg; ?>
  <?php endforeach; ?>
  <a href="#" class="close">x</a>
</div>
<?php unset($_SESSION['sysmsg']); endif; ?>

<div class="row">

<h3 class="large-4 columns"><strong><?= $itemsCount; ?></strong> Events</h3>

<?php if(user_is_logged_in()) : ?>
  <a class="small button round right" href="<?= base_path(); ?>Eventformular">+ Event hinzufügen</a><br />
<?php else : ?>
  <a class="small secondary button round right" href="<?= base_path(); ?>user/login">+ Event hinzufügen</a><br />
<?php endif; ?>

</div>
<div class="divider"></div>

<h4 class="">Filter</h4>
<form class="" action='<?= $pathThisFile; ?>' method='POST' enctype='multipart/form-data'>

  <select name="tag" size="<?= $counttags; ?>">
  <option value="0" selected="selected" >Tags</option>
  <?php foreach ($resulttags as $row) : ?>
    <option value="<?= $row->KID; ?>"><?= $row->kategorie; ?></option>
  <?php endforeach; ?>
  </select>
  <input type="submit" class="small button right" id="eventSubmit" name="submit" value="Nach Tags filtern">

</form>

<div class="divider"></div>

<div id="events" class="row" style="padding: 15px 0;">


<?php foreach($resultevents as $event): ?>
  <p><?= $event->start; ?><a href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>"><?= $event->name; ?></a>: <?= $event->kurzbeschreibung; ?></p><br>
<?php endforeach; ?>

</div>

<div class="divider"></div>

<div class="row">
  <ul class="pagination large-4 columns large-offset-5" style="padding-top:15px;">
    <li class="arrow"><a href="<?= base_path(); ?>Events/1">&laquo;</a></li>

    <?php for ($i=1; $i<=$maxPages; $i++) {
     if ($i == $currentPageNr) echo '<li class="current"><a href="#">'.$i.'</a></li>';
     else echo '<li><a href="'.base_path().'Events/'.$i.'">'.$i.'</a></li>';
     //<!-- <li class="unavailable"><a href="">&hellip;</a></li>-->
     } ?>

    <li class="arrow"><a href="<?= base_path(); ?>Events/<?= $maxPages ?>">&raquo;</a></li>
 </ul>
</div>
