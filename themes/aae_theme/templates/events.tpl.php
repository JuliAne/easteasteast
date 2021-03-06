<?php 
  // TODO: - Ausgabe von $resultEvents in sub-template speichern, um Ausgabe
  //         in z.B. Widgets zu ermöglichen. Evtl. wie new events::printWidget(events : obj, [range = 5, paginator = false, paginatorPage = 0])
  //       - Evtl. paginator-Klasse definieren?
  //       - Optimierung der Performance und Lesbarkeit
  
  $recurringEventTypes = array(
   '2' => t('Wöchentliche Wiederholung'),
   '3' => t('2-wöchentliche Wiederholung'),
   '4' => t('Monatliche Wiederholung'),
   '5' => t('2-monatliche Wiederholung')
  ); ?>

<header id="eventsPageHeader" class="pageHeader">
  <h2><?= $itemsCount; ?> Veranstaltungen & Events <a href="<?= base_path(); ?>events/rss" title="<?= t('Alle Events als RSS-Feed'); ?>"><img id="svg_logo" src="<?= base_path().path_to_theme(); ?>/img/rss.svg" /></a></h2>
  <p>Finde Workshops, Kreativwerkstätten, Märkte, Versammlungen und mehr.</p>
</header>

<div id="events" class="row">

  <aside class="aae-sidebar large-3 columns right">

  <?php if(user_is_logged_in()) : ?>
   <a class="medium button hollow round larg-12 columns" href="<?= base_path(); ?>events/new"><?= t('+ Event hinzufügen'); ?></a><br />
  <?php else : ?>
   <a class="login_first medium button hollow round large-12 columns" href="<?= base_path(); ?>user/login" title="<?= t('Bitte zunächst einloggen.'); ?>"><?= t('+ Event hinzufügen'); ?> (<?= t('Login'); ?>)</a><br />
   <?php endif; ?>

  <div id="filter" class="large-12 columns">

   <header class="large-12 columns">
    <h4 class="left"><?= t('Filter'); ?></h4>
    <a id="toggleFilterForm" class="small button right hide-for-large" style="margin-left:5px;" href="#" title="<?= t('Zeige Filter'); ?>">&#x25B4;</a>
    <?php if (!empty($this->filter)) : ?>
    <a class="small secondary button right" href="<?= base_path(); ?>events/" title="<?= t('Alle Filter entfernen.'); ?>">✗</a>
    <?php endif; ?>
    <div class="divider hide-for-small-only hide-for-medium-only"></div>
   </header>

   <form id="filterForm" method="GET" action="<?= base_path(); ?>events<?= (isset($resultKalender) && !empty($resultKalender) ? '?presentation=calendar' : ''); ?>">

    <div class="large-12 columns">

     <label for="filterKeyword"><?= t('Schlagwort:'); ?></label>
     <input name="filterKeyword" id="filterKeywordInput" type="text" <?= (isset($this->filter['keyword']) ? 'value="'.$this->filter['keyword'].'"' : ''); ?>/>

     <label for="filterTags"><?= t('Tags:'); ?></label>
     <select name="filterTags[]" id="eventSpartenInput" multiple="multiple" class="tokenize">
     <?php foreach ($resultTags as $tag) : ?>
      <option value="<?= $tag->KID; ?>"<?= (@in_array($tag->KID,$this->filter['tags']) ? ' selected="selected"' : ''); ?>><?= $tag->kategorie; ?></option>
     <?php endforeach; ?>
     </select>

    </div>
    <div class="large-12 columns">

     <label for="filterBezirke"><?= t('Bezirke:'); ?></label>
     <select name="filterBezirke[]" id="eventBezirkInput" multiple="multiple" class="tokenize">
     <?php foreach ($resultBezirke as $bezirk) : ?>
      <option value="<?= $bezirk->BID; ?>"<?= (@in_array($bezirk->BID,$this->filter['bezirke']) ? ' selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
     <?php endforeach; ?>
     </select>

    </div>
    <div id="timespace" class="large-12 columns hide-for-small-only <hide-for-medium-only></hide-for-medium-only>"<?= (isset($resultKalender) && !empty($resultKalender) ? ' style="display:none;"' : ''); ?>>

     <label><?= t('Zeitraum:'); ?></label>

     <div class="slider" data-slider>
      <span class="slider-handle sh-1" data-slider-handle role="slider" tabindex="1"></span>
      <span class="slider-fill" data-slider-fill></span>
      <span class="slider-handle sh-2" data-slider-handle role="slider" tabindex="1"></span>
      <input type="hidden"><input type="hidden">
     </div>

      <ul>
      <?php
       $curMonth = date('m');

       for ($i=0; $i<= 6; $i++) {
        $month = ($curMonth + $i > 12) ? ($curMonth-12) + $i : $curMonth + $i;
        echo '<li data-month="'.($month < 10 ? '0' : '').$month.'.'.($month < $curMonth ? date('Y')+1 : date('Y')).'">'.$this->monat_short[($month < 10 ? '0' : '').$month].'</li>';
       } ?>
      </ul>
     </div>

     <div class="large-12 columns">
      <input type="submit" class="medium button large-12 columns" id="eventSubmit" name="submit" value="<?= t('Filter anwenden'); ?>">
     </div>
    
   </div>
  </form>
  
  <?php if (!empty($festivals)) : ?>
  <div class="tagcloud akteure-tc hide-for-small-only hide-for-medium-only large-12 columns" style="margin-top:0;">
   <h4><?= t('Festivals'); ?></h4>
   <ul>
   <?php foreach ($festivals as $festival) : ?>
    <li><a href="<?= base_path().$festival->alias; ?>" title="Festivalseite des <?= $festival->name; ?>'s"><?= $festival->name; ?></a>
   <?php endforeach; ?> 
   </ul>
   </div>
  <?php endif; ?>

  </aside>

  <div id="eventsContent" class="large-9 small-12 columns">

   <ul class="tabs" id="events-tabs" style="margin-bottom:22px;">
    <li class="tabs-title<?= ($this->getOldEvents || !empty($this->filter) ? '' : ' is-active'); ?>" title="<?= t('Alle kommenden Events'); ?>"><a href="<?= base_path(); ?>events"<?= ($this->getOldEvents || !empty($this->filter) ? '' : ' aria-selected="true"'); ?>><?= t('Demnächst'); ?></a></li>
    <li class="tabs-title<?= ($this->getOldEvents && empty($this->filter) ? ' is-active' : ''); ?>" title="<?= t('Alle vergangenen Events'); ?>"><a href="<?= base_path(); ?>events/old"<?= ($this->getOldEvents ? ' aria-selected="true"' : ''); ?>><?= t('Vergangene Events'); ?></a></li>
    <?php if (!empty($this->filter)) : ?><li class="tabs-title is-active"><a href="#" aria-selected="true"><?= t('Filterergebnisse'); ?> (<?= count($resultEvents); ?>)</a></li><?php endif; ?>
    <ul id="presentationFilter" class="button-group round large-4 columns hide-for-small-only right">
     <li class="right"><a href="<?= base_path(); ?>events" name="timeline" class="small button <?= ($this->presentationMode !== 'calendar' ? 'active' : 'secondary'); ?>" title="<?= t('Darstellung als Timeline'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/events-timeline-view.svg" /></a></li>
     <li class="right"><a href="<?= base_path(); ?>events/?presentation=calendar" name="calendar" class="small button <?= ($this->presentationMode == 'calendar' ? 'active' : 'secondary'); ?>" title="<?= t('Darstellung als Kalender'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/events-calendar-view.svg" /></a></li>
    </ul>

   </ul>

 <?php if (isset($resultKalender) && !empty($resultKalender)) : ?>

  <div id="aaeCalendar">
   <?= $resultKalender; ?>
  </div>

 <?php elseif (is_array($resultEvents) && !empty($resultEvents)) : ?>
 
  <?php foreach ($resultEvents as $key => $event): ?>  
   <?php global $base_root;
         $hasAdress = (!empty($event->adresse->strasse) && !empty($event->adresse->nr) && !empty($event->adresse->plz) && !empty($event->akteur->name));
   
    if ($event->start->format('m.Y') != $cur_month) : ?>
    <div class="large-12 columns"><h4><?= $this->monat_lang[$event->start->format('m')]; ?> <?= $event->start->format('Y'); ?></h4></div>
   <?php endif; $cur_month = $event->start->format('m.Y'); ?>

   <div class="large-6 columns small-12 columns aaeEvent<?= ($event->start->format('Y-m-d') == date('Y-m-d')) ? ' today' : ''; ?><?= (!empty($event->eventRecurringType) && ($event->eventRecurringType <= 5) ? ' eventRecurres' : ''); ?><?= ($event->eventRecurringType == 6 ? ' isFestival' : ''); ?>" itemscope itemtype="http://schema.org/Event">
   <!-- Some microdata to enrich events-snippets for alien engines -->
   <?php if (!empty($event->bild)) : ?>
   <meta itemprop="image" content="<?= $base_root.$event->bild; ?>" />
   <?php else : ?>
   <meta itemprop="image" content="<?= $base_root.path_to_theme(); ?>/img/logo_new_new.png" />
   <?php endif; ?>
   <meta itemprop="startDate" content="<?= $event->start->format('Y-m-dTH:i'); ?>" />
   <?php if (empty($event->eventRecurringType) && $event->ende->format('Ymd') != '10000101') : ?>
   <meta itemprop="endDate" content="<?= $event->ende->format('Y-m-dTH:i'); ?>" />
   <?php # TODO: ELSE ?!
         endif; ?>
   <meta itemprop="url" content="<?= $base_root .'/eventprofil/'.$event->EID; ?>" />
   <?php if (!empty($event->adresse->gps_lat)) : ?>
   <!--
   <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
    <meta itemprop="latitude" content="<?= $event->adresse->gps_lat; ?>" />
    <meta itemprop="longitude" content="<?= $event->adresse->gps_long; ?>" />
   </div>
   -->
   <?php endif; ?>
   <div itemprop="location" itemscope itemtype="http://schema.org/PostalAddress">
   <?php if ($hasAdress) : ?>
    <meta itemprop="name" content="<?= $event->akteur->name; ?>" />
    <meta itemprop="streetAddress" content="<?= $event->adresse->strasse.' '.$event->adresse->nr; ?>" />
    <meta itemprop="postalCode" content="<?= $event->adresse->plz; ?>" />
   <?php else : ?>
    <meta itemprop="name" content="Leipziger Osten" />
    <meta itemprop="streetAddress" content="<?= t('Keine Angabe'); ?>" />
    <meta itemprop="postalCode" content="04315" />
   <?php endif; ?>
    <meta itemprop="addressLocality" content="Leipzig" />
   </div>

   <div class="date large-2 small-3 columns button secondary round" title="<?= t('Start'); ?>: <?= $event->start->format('d') . '. ' . $this->monat_lang[$event->start->format('m')] . ' ' . $event->start->format('Y') . ($hasAdress ? ' in '. $event->adresse->plz .' '. $event->adresse->bezirksname : ''); ?>"><?= $event->start->format('d'); ?><br /><?= $this->monat_short[$event->start->format('m')]; ?></div>
   <div class="content large-9 small-9 columns">
   <header<?= (!empty($event->eventRecurringType) ? ' title="'.$recurringEventTypes[$event->eventRecurringType].'"' : ''); ?>>
    <h3><a href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>" itemprop="name" content="<?= htmlspecialchars($event->name); ?>" title="<?= t('Eventprofil aufrufen'); ?>"><?= $event->name; ?></a><?= ($event->eventRecurringType == 6 ? ' - '.$event->festival->name : ''); ?></h3>
    <p class="aaeEventDate"><span><?= ($event->start->format('Y-m-d') == date('Y-m-d')) ? '<a href="#">'.t('Heute').',</a> ' : ''; ?><?php if($event->start->format('H:i') !== '00:00') echo $event->start->format('H:i'); ?><?php if($event->ende->format('H:i') !== '00:00') echo ' - '. $event->ende->format('H:i'); ?><?= (!empty($event->eventRecurringType) ? '  '.$recurringEventTypes[$event->eventRecurringType] : ''); ?></span></p>
    <p class="aaeEventTags">
    <?php foreach($event->tags as $tag) : ?>
     <a class="tag" href="<?= base_path(); ?>events/?filterTags[]=<?= $tag->KID; ?>" rel="nofollow" title="<?= t('Zeige alle mit !kategorie getaggten Events',array('!kategorie'=>$tag->kategorie)); ?>">#<?= $tag->kategorie; ?></a>
    <?php endforeach; ?>
    </p>
   </header>
   <?php if (!empty($event->kurzbeschreibung) && (empty($event->eventRecurringType))) : ?>
    <div class="divider"></div>
    <div class="event-content">
      <?php $numwords = 30; preg_match("/(\S+\s*){0,$numwords}/", $event->kurzbeschreibung, $regs); ?>
      <div class="eventDesc" itemprop="description"><p><?= strip_tags(trim($regs[0])); ?> <a class="weiterlesen" href="<?= base_path().'eventprofil/'.$event->EID; ?>" title="<?= t('Eventprofil aufrufen'); ?>">... <?= t('zum Event'); ?></a></p></div>
    </div>
   <?php else : ?>
    <meta itemprop="description" content="<?= strip_tags(trim($regs[0])); ?>" />
   <?php endif; ?>
   </div>
   
   <?php if (!empty($event->akteur)) : ?>
   <div class="akteurData large-10 small-12 columns">
    <p><a href="<?= base_path().'akteurprofil/'.$event->akteur->AID; ?>" title="<?= t('Akteurprofil von !username besuchen', array('!username' => $event->akteur->name)); ?>"><img src="<?= $event->akteur->bild; ?>" /><?= $event->akteur->name; ?></a></p>
   </div>
   <?php endif; ?>

  </div>
 <?php endforeach; else : ?>

   <p style="text-align:center;"><?= t('Es wurden leider keine Events mit diesen Angaben gefunden...'); ?></p>
   <p style="text-align:center;"><a href="<?= base_path(); ?>events"><?= t('Alle Filter entfernen.'); ?></a></p>

 <?php endif; ?>

</div>

<div class="row">
<!--  <ul class="pagination large-4 columns large-offset-5" style="padding-top:15px;">
    <li class="arrow"><a href="<?= base_path(); ?>Events/1">&laquo;</a></li>

    <?php for ($i=1; $i<=$maxPages; $i++) {
     if ($i == $currentPageNr) echo '<li class="current"><a href="#">'.$i.'</a></li>';
     else echo '<li><a href="'.base_path().'events/'.$i.'">'.$i.'</a></li>';
     //<!-- <li class="unavailable"><a href="">&hellip;</a></li>-->
     } ?>

    <li class="arrow"><a href="<?= base_path(); ?>Events/<?= $maxPages ?>">&raquo;</a></li>
 </ul> -->


  <div class="large-12 columns">

   <div class="divider"></div>

   <aside class="tagcloud">
    <h4><img class="cloudimg" src="<?= base_path().path_to_theme(); ?>/img/cloud.svg" /><?= t('Populäre Tags'); ?></h4>

    <?php foreach ($resultTagCloud as $tag) : ?>
      <a class="tagc-<?= (($tag->count / 3) >= 5 ? '5' : ceil($tag->count / 3)); ?> tag" href="<?= base_path(); ?>events/?filterTags[]=<?= $tag->KID; ?>" rel="nofollow">#<?= $tag->kategorie; ?></a>
    <?php endforeach; ?>
    </aside>

  </div>
 </div>
