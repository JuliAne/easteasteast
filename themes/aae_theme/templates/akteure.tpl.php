<div class="row">

 <h3 class="large-4 columns"><strong><?= $itemsCount; ?></strong> Akteure</h3>

<?php if(user_is_logged_in()) : ?>
  <a class="medium button hollow right" href="<?= base_path(); ?>Akteurformular">+ Akteur hinzufügen</a><br />
<?php else : ?>
  <a class="login_first medium button hollow right" href="<?= base_path(); ?>user/login" title="Bitte zunächst einloggen.">+ Akteur hinzufügen (Login)</a><br />
<?php endif; ?>

</div>
<div class="divider"></div>

<div id="akteure" class="row" style="padding-top:15px;">

 <aside class="aae-sidebar large-3 columns right">
  <div id="filter" class="large-12 columns">

  <div class="large-12 columns" id="removeFilter">
   <h4 class="left">Filter</h4>
   <a class="small button right hide-for-medium" style="padding:4px 10px;margin-left:5px;" href="#" title="Zeige Filter" onclick="javascript:$('#filter .large-12').slideDown(400);">&#x25BE;</a>
   <a class="small secondary button right" style="padding:4px 10px;" href="<?= base_path(); ?>akteure" title="Alle Filter entfernen">X</a>
   <div class="divider"></div>
  </div>

  <form id="filterForm" method="get" action="<?= base_path(); ?>akteure">
   <div class="large-12 columns">
    <label for="filterKeyword">Schlagwort:</label>
    <input name="filterKeyword" id="filterKeywordInput" type="text" <?= (isset($this->filter['keyword']) ? 'value="'.$this->filter['keyword'].'"' : ''); ?>/>
   </div>

   <div class="large-12 columns">
    <label for="filterTags">Tags:</label>
    <select name="filterTags[]" id="eventSpartenInput" multiple="multiple" class="tokenize">
    <?php foreach ($resultTags as $tag) : ?>
     <option value="<?= $tag->KID; ?>"<?= ($this->filteredTags[$tag->KID] == $tag->KID ? ' selected="selected"' : ''); ?>><?= $tag->kategorie; ?></option>
    <?php endforeach; ?>
    </select>
   </div>

   <div class="large-12 columns">
    <label for="filterBezirke">Bezirke:</label>
    <select name="filterBezirke[]" id="eventBezirkInput" multiple="multiple" class="tokenize">
    <?php foreach ($resultBezirke as $bezirk) : ?>
     <option value="<?= $bezirk->BID; ?>"<?php echo ($this->filteredBezirke[$bezirk->BID] == $bezirk->BID ? ' selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
    <?php endforeach; ?>
    </select>
   </div>

  <div class="large-5 small-6 columns">
   <label for="display_number">Anzahl:</label>
   <select name="display_number" id="displayNumber">
    <option value="15" <?= ($this->maxAkteure == '15' ? 'selected="selected"' : ''); ?>>15</option>
    <option value="20" <?= ($this->maxAkteure == '20' ? 'selected="selected"' : ''); ?>>20</option>
    <option value="30" <?= ($this->maxAkteure == '30' ? 'selected="selected"' : ''); ?>>30</option>
    <option value="all" <?= ($this->maxAkteure == 'all' ? 'selected="selected"' : ''); ?>>Alle</option>
   </select>
  </div>


  <div id="change-style" class="large-7 columns">
   <ul id="presentationFilter" class="button-group round" style="margin-top:27px;">
    <li><a href="#" name="boxen" class="small button <?php echo ($this->presentationMode !== 'map' ? 'active' : 'secondary'); ?>" title="Darstellung als Boxen"><img src="<?= base_path().path_to_theme(); ?>/img/ios-grid-view-outline.svg" /></a></li>
    <li><a href="#" name="map" class="small button <?php echo ($this->presentationMode == 'map' ? 'active' : 'secondary'); ?>" title="Darstellung auf Karte"><img src="<?= base_path().path_to_theme(); ?>/img/map.svg" /></a></li>
   </ul>
  </div>

  <div class="large-12 columns">
   <input type="submit" class="large-12 columns medium button" id="sendFilters" name="submit" value="Filter anwenden">
  </div>

 </form>
 </div>

 <div class="tagcloud akteure-tc large-12 columns">
  <h4>Bezirke nach Häufigkeit</h4>
  <ul>
   <?php foreach ($resultBezirkeRelevance as $bez) : ?>
    <li><a href="<?= base_path(); ?>akteure/?filterBezirke[]=<?= $bez->BID; ?>" rel="nofollow"><?= $bez->bezirksname; ?> - <?= $bez->count; ?></a></li>
   <?php endforeach; ?>
 </ul>
</div>

</aside>

<div id="akteure-content" class="large-9 columns">

 <?php if ($this->hasFilters) : ?>
 <ul class="tabs" data-tabs id="events-tabs" style="margin-bottom:22px;">
  <li class="tabs-title is-active"><a href="#" aria-selected="true">Filterergebnisse (<?= count($resultAkteure); ?>)</a></li>
  <li class="tabs-title"><a href="<?= base_path(); ?>akteure">Alle Akteure</a></li>
 </ul>
 <?php endif; ?>

<?php if ($this->presentationMode == 'map') : ?>
 <div id="map" style="width: 100%; height: 400px;"></div>
<?php else : ?>

<?php if (is_array($resultAkteure) && !empty($resultAkteure)) : ?>

<?php foreach($resultAkteure as $akteur): ?>
  <div class="large-4 large-offset-1 small-5 small-offset-1 columns pcard">
   <header <?= (!empty($akteur->bild) ? 'style="background-image:url('.$akteur->bild.');"' : ''); ?><?= ($akteur->renderSmallName ? ' class="renderSmallName"' : ''); ?>>
     <h3><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>" title="Akteurprofil besuchen"><?= $akteur->name; ?></a></h3>
    </header>
    <section>
      <?php if (!empty($akteur->bezirk)) : ?><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p><?php endif; ?>
      <?php if (!empty($akteur->beschreibung)): ?>
      <div class="divider"></div>
        <p><?= $akteur->kurzbeschreibung; ?> <a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>" title="Akteurprofil besuchen">...</a></p>
      <?php endif; ?>
    </section>
   </div>
 <?php endforeach; ?>

 <?php else : ?>
  <p style="text-align:center;">Es wurden leider keine Akteure mit diesen Angaben gefunden.</p>
  <p style="text-align:center;"><a href="<?= base_path(); ?>akteure">Filter löschen.</a></p>
 <?php endif; ?>

<?php endif; ?>

 </div>
</div>

<div class="divider" style="margin-top:25px;"></div>

<?php if ($this->presentationMode !== 'map' && !$this->hasFilters) : ?>
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
