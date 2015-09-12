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

<div id="filter" class="row" style="padding-top:22px;margin-bottom:5px;">

<div class="large-2 columns" style="padding-top:8px;">
  <h4 class="left">Filter</h4>
  <a class="small secondary button round right" style="padding:4px 10px;" href="#" title="Alle Filter löschen">x</a>
 </div>

 <div class="large-3 large-offset-2 columns">

 <form action='<?=  $pathThisFile; ?>' method='POST' enctype='multipart/form-data'>

   <select name="tag">
   <option value="0" selected="selected" >Tag-Filter</option>
   <?php foreach ($resulttags as $row) : ?>
     <option value="<?= $row->KID; ?>"><?= $row->kategorie; ?></option>
   <?php endforeach; ?>
   </select>

 </div>

<div id="change-style" class="button-bar large-5 columns">
  <ul class="button-group round">
    <li><a href="#" class="small button success" title="Darstellung in Timeline"><img src="<?= base_path().path_to_theme(); ?>/img/ios-list-outline.svg" /></a></li>
    <li><a href="#" class="small button secondary" title="Darstellung im Kalender"><img src="<?= base_path().path_to_theme(); ?>/img/ios-grid-view-outline.svg" /></a></li>
    <li><a href="#" class="small button secondary" title="Darstellung auf Karte"><img src="<?= base_path().path_to_theme(); ?>/img/map.svg" /></a></li>
  </ul>
  <input type="submit" class="small button right" id="eventSubmit" name="submit" value="OK">
</div>

</form>

</div>

<div class="divider"></div>

<div id="events" class="row" style="padding: 15px 0;">


<?php foreach($resultevents as $event): ?>
  <p><?= $event->start; ?><a style="line-height:1.6em;" href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>"> <strong><?= $event->name; ?></strong></a>
  <?php if ($event->kurzbeschreibung!=''): ?>: <?= $event->kurzbeschreibung; ?><?php endif; ?></p><br />
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
