<?php if (!empty($_SESSION['sysmsg'])) : ?>
<div id="alert">
  <?php foreach ($_SESSION['sysmsg'] as $msg): ?>
    <?= $msg; ?>
  <?php endforeach; ?>
  <a href="#" class="close">x</a>
</div>
<?php unset($_SESSION['sysmsg']); endif; ?>

<div class="row">

<h3 class="large-4 columns"><strong><?= $itemsCount; ?></strong> Akteure</h3>

<?php if(user_is_logged_in()) : ?>
  <a class="small button round right" href="<?= base_path(); ?>Akteurformular">+ Akteur hinzufügen</a><br />
<?php else : ?>
  <a class="small secondary button round right" href="<?= base_path(); ?>user/register">+ Akteur hinzufügen</a><br />
<?php endif; ?>

</div>
<div class="divider"></div>

<div id="filter" class="row">

 <div class="large-1 columns" id="removeFilter">
  <a class="small secondary button round right" style="padding:4px 10px;" href="#" title="Alle Filter entfernen">x</a>
 </div>

 <div class="large-6 large-offset-1 columns">

 <form action='<?=  $pathThisFile; ?>' method='POST' enctype='multipart/form-data'>

   <label for="tag">Nach Tags filtern:</label>
   <select name="tag" id="eventSpartenInput" multiple="multiple" class="tokenize">
   <?php foreach ($resulttags as $row) : ?>
     <option value="<?= $row->KID; ?>"><?= $row->kategorie; ?></option>
   <?php endforeach; ?>
   </select>

 </div>

<div id="change-style" class="button-bar large-4 columns">
  <ul class="button-group round">
    <li><a href="#" class="small button" title="Darstellung als Boxen"><img src="<?= base_path().path_to_theme(); ?>/img/ios-list-outline.svg" /></a></li>
    <!--<li><a href="#" class="small button secondary" title="Darstellung im Kalender"><img src="<?= base_path().path_to_theme(); ?>/img/ios-grid-view-outline.svg" /></a></li>-->
    <li><a href="#" class="small button secondary" title="Darstellung auf Karte"><img src="<?= base_path().path_to_theme(); ?>/img/map.svg" /></a></li>
  </ul>
  <input type="submit" class="small button right" id="akteurSubmit" name="submit" value="Filter anwenden">
</div>

</form>

</div>
<div class="divider"></div>

<div id="akteure" class="row" style="padding: 15px 0;">

<?php foreach($resultAkteure as $akteur): ?>
  <div class="large-3 large-offset-1 columns pcard" style="margin-top:10px;">
   <header <?php if($akteur->bild != '') echo 'style="background:url('.$akteur->bild.');"'; ?>>
     <h3><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>"><?= $akteur->name; ?></a></h3>
    </header>
    <section>
     <?php if ($akteur->beschreibung !== ''): ?>
     <p><?= $kurzbeschreibung = substr ($akteur->beschreibung, 0, 120)."..."; ?></p>
     <?php endif; ?>
     <p><a href="<?= base_path(); ?>Akteurprofil/<?= $akteur->AID; ?>">Zum Projekt...</a></p>
    </section>
   </div>
<?php endforeach; ?>

</div>

<div class="divider"></div>

<div class="row">
  <ul class="pagination large-4 columns large-offset-5" style="padding-top:15px;">
    <li class="arrow"><a href="<?= base_path(); ?>Akteure/1">&laquo;</a></li>

    <?php for ($i=1; $i<=$maxPages; $i++) {
     if ($i == $currentPageNr) echo '<li class="current"><a href="#">'.$i.'</a></li>';
     else echo '<li><a href="'.base_path().'Akteure/'.$i.'">'.$i.'</a></li>';
     //<!-- <li class="unavailable"><a href="">&hellip;</a></li>-->
     } ?>

    <li class="arrow"><a href="<?= base_path(); ?>Akteure/<?= $maxPages ?>">&raquo;</a></li>
 </ul>
</div>
