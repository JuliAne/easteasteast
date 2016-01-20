<?php if (!empty($_SESSION['sysmsg'])) : ?>
<div id="alert">
  <?php foreach ($_SESSION['sysmsg'] as $msg): ?>
    <?= $msg; ?>
  <?php endforeach; ?>
  <a href="#" class="close">x</a>
</div>
<?php unset($_SESSION['sysmsg']); endif; ?>

<?php
 $monat = array(
  '01' => 'Jan',
  '02' => 'Feb',
  '03' => 'Mär',
  '04' => 'Apr',
  '05' => 'Mai',
  '06' => 'Jun',
  '07' => 'Jul',
  '08' => 'Aug',
  '09' => 'Sep',
  '10' => 'Okt',
  '11' => 'Nov',
  '12' => 'Dez',
 );
 $monat_lang = array(
  '01' => 'Januar',
  '02' => 'Februar',
  '03' => 'März',
  '04' => 'April',
  '05' => 'Mai',
  '06' => 'Juni',
  '07' => 'Juli',
  '08' => 'August',
  '09' => 'September',
  '10' => 'Oktober',
  '11' => 'November',
  '12' => 'Dezember',
 );
?>

<div class="row">

<h3 class="large-4 columns"><strong><?= $itemsCount; ?></strong> Events<a href="<?= base_path(); ?>events/rss" title="Alle Events als RSS-Feed"><img id="svg_logo" src="<?= base_path().path_to_theme(); ?>/img/rss.svg" /></a></h3>

<?php if(user_is_logged_in()) : ?>
  <a class="medium button hollow round right" href="<?= base_path(); ?>Eventformular">+ Event hinzufügen</a><br />
<?php else : ?>
  <a class="login_first medium button hollow round right" href="<?= base_path(); ?>user/login" title="Bitte zunächst einloggen.">+ Event hinzufügen (Login)</a><br />
<?php endif; ?>

</div>
<div class="divider"></div>

<div id="events" class="row" style="padding: 15px 0;">

  <div id="filter" class="large-3 columns">

   <div class="large-12 columns" id="removeFilter">
    <h4 class="left">Filter</h4>
    <a class="small secondary button right hollow hide-for-medium" style="padding:4px 10px;" href="#" title="Zeige Filter" onclick="javascript:$('#filter .large-12').slideDown(400);">&#x25BE;</a>
    <a class="small secondary button right" style="padding:4px 10px;" href="<?= base_path(); ?>events/" title="Alle Filter entfernen">x</a>
    <div class="divider"></div>
   </div>

   <div class="large-12 columns">

     <form id="filterForm" method="GET" action="<?= base_path(); ?>events/<?= $currentPageNr; ?>">

     <label for="tag">Tags:</label>
     <select name="tags[]" id="eventSpartenInput" multiple="multiple" class="tokenize">
     <?php foreach ($resultTags as $row) : ?>
       <option value="<?= $row->KID; ?>"<?php echo ($filterTags[$row->KID] == $row->KID ? ' selected="selected"' : ''); ?>><?= $row->kategorie; ?></option>
     <?php endforeach; ?>
   </select>

   </div>

   <div class="large-12 columns">

     <label for="tag">Bezirke:</label>
     <select name="bezirk[]" id="eventBezirkInput" multiple="multiple" class="tokenize">
     <?php foreach ($resultBezirke as $row) : ?>
       <option value="<?= $row->BID; ?>"<?php echo ($filterTags[$row->BID] == $row->BID ? ' selected="selected"' : ''); ?>><?= $row->bezirksname; ?></option>
     <?php endforeach; ?>
   </select>

   </div>

  <div id="change-style" class="button-bar large-12 columns">
    <label>Darstellung:</label>
    <ul id="presentationFilter" class="button-group round large-12 columns">
      <li><a href="#" name="timeline" class="small button <?php echo ($this->presentationMode !== 'kalender' ? 'active' : 'secondary'); ?>" title="Darstellung als Timeline"><img src="<?= base_path().path_to_theme(); ?>/img/ios-list-outline.svg" /></a></li>
      <li><a href="#" name="kalender" class="small button <?php echo ($this->presentationMode == 'kalender' ? 'active' : 'secondary'); ?>" title="Darstellung als Kalender"><img src="<?= base_path().path_to_theme(); ?>/img/ios-grid-view-outline.svg" /></a></li>
      <!--<li><a href="#" class="small button secondary" title="Darstellung auf Karte"><img src="<?= base_path().path_to_theme(); ?>/img/map.svg" /></a></li>-->
    </ul>

    <label>Zeitraum:</label>

    <div class="large-12 columns slider" data-slider data-initial-start="1" data-initial-end="40" data-end="100">
     <span class="slider-handle" data-slider-handle role="slider" tabindex="1"></span>
     <span class="slider-fill" data-slider-fill></span>
     <span class="slider-handle" data-slider-handle role="slider" tabindex="1"></span>
     <input type="hidden">
     <input type="hidden">
    </div>

    <input type="submit" class="medium button large-12 columns" id="eventSubmit" name="submit" value="Filter anwenden">
    </form>
   </div>

  </div>
<!--
  <div class="large-3 columns" style="position:absolute;margin-top:500px;">
   <h4>Tag's</h4>
   <a href="#">Jazz</a>
   <a href="#">Bla</a>
   <a href="#">TolleDinge</a>
   <a href="#">Jazz</a>
   <a href="#">Huhu</a>
   <a href="#">Jazz</a>
  <div class="divider"></div>
</div>-->


  <div id="events_content" class="large-offset-1 large-8 columns">

 <?php if(isset($resultKalender) && !empty($resultKalender)){

   echo '<div id="aae_calendar">';
   echo $resultKalender;
   echo '</div>';

 } else if (is_array($resultEvents) && !empty($resultEvents)) {

  //print_r($resultEvents);
  //print_r('Hello World');
  //print_r($datesort);
  //print_r('Hello World');
  //print_r($resultEventsBool);

 $cur_month = '';
 //$cur_year = date('Y');
 $cur_date = (int) date('Ymd');
 $heading_future = 0;
 $heading_present = 0;
 $heading_past = 0;

 foreach($resultEvents as $key => $event): ?>

  <?php

  if ($heading_future == 0 && $event->datesum > $cur_date) {
    echo '<h4 class="heading-event">DEMNÄCHST</h4>';
    echo '<div class="large-6 columns small-6 columns aaeEvent future-first">';
    $heading_future = 1;
  } elseif ($heading_present == 0 && $event->datesum == $cur_date) {
    echo '<h4 class="heading-event">HEUTE</h4>';
    echo '<div class="large-6 columns small-6 columns aaeEvent present-first">';
    $heading_present = 1;
  } elseif ($heading_past == 0 && $event->datesum < $cur_date) {
    echo '<h4 class="heading-event">VERGANGEN</h4>';
    echo '<div class="large-6 columns small-6 columns aaeEvent past-first">';
    $heading_past = 1;
  } else {
    echo '<div class="large-6 columns small-6 columns aaeEvent">';
  }

  if ($event->datemonth != $cur_month) {
    echo '<h4>'.$monat_lang[$event->datemonth].' '.$event->dateyear.'</h4>';
  }

  $cur_month = $event->datemonth;

  ?>

  <div class="date large-2 columns button secondary round"><?= $event->dateday; ?><br /><?= $monat[$event->datemonth]; ?></div>
  <div class="content large-9 columns">
   <header>
    <p><a style="line-height:1.6em;" href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>"> <strong><?= $event->name; ?></strong></a>
    <span class=""><?= $event->zeit_von; ?> - <?= $event->zeit_bis; ?></span></p>
    <p>
   <?php //print_r($event); ?>
     <?php foreach($event->tags as $tag) : ?>
     <a class="tag" href="<?= base_path(); ?>events/?tags[]=<?= $tag->KID; ?>">#<?= $tag->kategorie; ?></a>
   <?php endforeach; ?>
    </p>
   </header>
  <?php if (!empty($event->kurzbeschreibung)): ?>
    <div class="divider"></div>
    <?php $numwords = 30;
          preg_match("/(\S+\s*){0,$numwords}/", $event->kurzbeschreibung, $regs); ?>
    <p><?= trim($regs[0]); ?>...</p>
  <?php endif; ?>
  </div>

  <div class="akteurData large-10 columns">
   <?php foreach ($event->akteur as $akteur) : ?>
    <p><a href="<?= base_path().'akteurprofil/'.$akteur->AID; ?>" title="Profil von <?= $akteur->name; ?>"><img src="<?= $akteur->bild; ?>" /><?= $akteur->name; ?></a></p>
   <?php endforeach; ?>
  </div>

 </div>
 <?php endforeach; }  else { ?>

   <p style="text-align:center;">Es wurden leider keine Events mit diesen Angaben gefunden...</p>
   <p style="text-align:center;"><a href="<?= base_path(); ?>events">Filter entfernen.</a></p>

  <?php } ?>

</div>
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
