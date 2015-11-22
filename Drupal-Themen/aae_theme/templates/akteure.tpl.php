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
  <a class="login_first small secondary button round right" href="<?= base_path(); ?>user/login" title="Bitte zunächst einloggen.">+ Akteur hinzufügen (Login)</a><br />
<?php endif; ?>

</div>
<div class="divider"></div>

<div id="filter" class="row">

 <form id="filterForm" method="get" action="<?= base_path(); ?>Akteure/<?= $currentPageNr; ?>">

 <div class="large-1 columns" id="removeFilter">
  <a class="small secondary button round right" style="padding:4px 10px;" href="/Akteure" title="Alle Filter entfernen">x</a>
 </div>

 <div class="large-4 large-offset-1 columns">

   <label for="tag">Nach Tags filtern:</label>
   <select name="tags[]" id="eventSpartenInput" multiple="multiple" class="tokenize">
   <?php // show $this->sparten ?>
   <?php foreach ($resulttags as $row) : ?>
     <option value="<?= $row->KID; ?>"><?= $row->kategorie; ?></option>
   <?php endforeach; ?>
   </select>

 </div>

 <div class="large-2 columns">
  <label for="display_number">Anzahl:</label>
  <select name="display_number" id="displayNumber">
   <option value="10" <?= ($this->maxAkteure == '10' ? 'selected="selected"' : ''); ?>>10</option>
   <option value="15" <?= ($this->maxAkteure == '15' ? 'selected="selected"' : ''); ?>>15</option>
   <option value="20" <?= ($this->maxAkteure == '20' ? 'selected="selected"' : ''); ?>>20</option>
   <option value="all" <?= ($this->maxAkteure == 'all' ? 'selected="selected"' : ''); ?>>Alle</option>
  </select>
 </div>


 <div id="change-style" class="button-bar large-4 columns">
  <ul id="presentationFilter" class="button-group round">
    <li><a href="#" name="boxen" class="small button <?php echo ($this->presentationMode !== 'map' ? 'active' : 'secondary'); ?>" title="Darstellung als Boxen"><img src="<?= base_path().path_to_theme(); ?>/img/ios-grid-view-outline.svg" /></a></li>
    <li><a href="#" name="map" class="small button <?php echo ($this->presentationMode == 'map' ? 'active' : 'secondary'); ?>" title="Darstellung auf Karte"><img src="<?= base_path().path_to_theme(); ?>/img/map.svg" /></a></li>
  </ul>
  <input type="submit" class="small button right" id="sendFilters" name="submit" value="Filter anwenden">
 </div>

</form>

</div>
<div class="divider"></div>

<div id="akteure" class="row" style="padding: 15px 0;">

<?php if ($this->presentationMode == 'map') : ?>
  <div id="map" style="width: 100%; height: 400px;"></div>
<?php else : ?>

<?php foreach($resultAkteure as $akteur): ?>
  <div class="large-3 large-offset-1 columns pcard" style="margin-top:10px;">
   <header <?php if($akteur->bild != '') echo 'style="background-image:url('.$akteur->bild.');"'; ?>>
     <h3><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>"><?= $akteur->name; ?></a></h3>
    </header>
    <section>
      <p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" />Leipzig, Reudnitz</strong></p>
      <?php if (!empty($akteur->beschreibung)): ?>
      <div class="divider"></div>
      <p><?= substr($akteur->beschreibung, 0, 145)."..."; ?></p>
     <?php endif; ?>
    </section>
   </div>
 <?php endforeach; ?>

<?php endif; ?>

</div>

<div class="divider"></div>

<?php if ($this->presentationMode !== 'map') : ?>
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
<?php endif; ?>
