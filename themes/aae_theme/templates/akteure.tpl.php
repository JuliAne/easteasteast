<div class="row">

 <h3 class="large-4 columns"><strong><?= $itemsCount; ?></strong> Akteure</h3>

<?php if(user_is_logged_in()) : ?>
  <a class="medium button hollow right" href="<?= base_path(); ?>Akteurformular">+ Akteur hinzufügen</a><br />
<?php else : ?>
  <a class="login_first medium button hollow right" href="<?= base_path(); ?>user/login" title="Bitte zunächst einloggen.">+ Akteur hinzufügen (Login)</a><br />
<?php endif; ?>

 </div>
<div class="divider"></div>

<div id="filter" style="padding-top:15px;" class="row">

 <form id="filterForm" method="get" action="<?= base_path(); ?>akteure/<?= $currentPageNr; ?>">

 <div class="large-1 columns" id="removeFilter" style="margin-top:14px;">
  <a class="small secondary button right" style="padding:4px 10px;" href="<?= base_path(); ?>akteure" title="Alle Filter entfernen">x</a>
 </div>

 <div class="large-4 small-6 columns">

   <label for="tag">Nach Tags filtern:</label>
   <select name="tags[]" id="eventSpartenInput" multiple="multiple" class="tokenize">
   <?php foreach ($resulttags as $row) : ?>
     <option value="<?= $row->KID; ?>"<?php echo ($filterTags[$row->KID] == $row->KID ? ' selected="selected"' : ''); ?>><?= $row->kategorie; ?></option>
   <?php endforeach; ?>
   </select>

 </div>

 <div class="large-1 small-6 columns">
  <label for="display_number">Anzahl:</label>
  <select name="display_number" id="displayNumber">
   <option value="10" <?= ($this->maxAkteure == '10' ? 'selected="selected"' : ''); ?>>10</option>
   <option value="15" <?= ($this->maxAkteure == '15' ? 'selected="selected"' : ''); ?>>15</option>
   <option value="20" <?= ($this->maxAkteure == '20' ? 'selected="selected"' : ''); ?>>20</option>
   <option value="all" <?= ($this->maxAkteure == 'all' ? 'selected="selected"' : ''); ?>>Alle</option>
  </select>
 </div>


 <div id="change-style" class="button-bar large-3 columns" style="margin-top:20px;">
  <ul id="presentationFilter" class="button-group round">
    <li><a href="#" name="boxen" class="small button <?php echo ($this->presentationMode !== 'map' ? 'active' : 'secondary'); ?>" title="Darstellung als Boxen"><img src="<?= base_path().path_to_theme(); ?>/img/ios-grid-view-outline.svg" /></a></li>
    <li><a href="#" name="map" class="small button <?php echo ($this->presentationMode == 'map' ? 'active' : 'secondary'); ?>" title="Darstellung auf Karte"><img src="<?= base_path().path_to_theme(); ?>/img/map.svg" /></a></li>
  </ul>
 </div>

 <input type="submit" class="large-2 large-offset-2 columns medium button" style="margin-top: 25px;" id="sendFilters" name="submit" value="Filter anwenden">

</form>

</div>
<div class="divider"></div>

<div id="akteure" class="row" style="padding: 15px 0;">

<?php if ($this->presentationMode == 'map') : ?>
  <div id="map" style="width: 100%; height: 400px;"></div>
<?php else : ?>

<?php if (is_array($resultAkteure) && !empty($resultAkteure)) : ?>

<?php foreach($resultAkteure as $akteur): ?>
  <div class="large-3 large-offset-1 small-5 small-offset-1 columns pcard" style="margin-top:10px;">
   <header <?php if($akteur->bild != '') echo 'style="background-image:url('.$akteur->bild.');"'; ?>>
     <h3><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>" title="Akteurprofil besuchen"><?= $akteur->name; ?></a></h3>
    </header>
    <section>
      <?php if (!empty($akteur->bezirk)) : ?><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p><?php endif; ?>
      <?php if (!empty($akteur->beschreibung)): ?>
      <div class="divider"></div>
        <?php $numwords = 30;
              preg_match("/(\S+\s*){0,$numwords}/", $akteur->beschreibung, $regs); ?>
        <p><?= trim($regs[0]); ?><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>" title="Akteurprofil besuchen">...</a></p>
      <?php endif; ?></p>
    </section>
   </div>
 <?php endforeach; ?>

 <?php else : ?>
  <p style="text-align:center;">Es wurden leider keine Akteure mit diesen Angaben gefunden.</p>
  <p style="text-align:center;"><a href="<?= base_path(); ?>akteure">Filter löschen.</a></p>
 <?php endif; ?>

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
