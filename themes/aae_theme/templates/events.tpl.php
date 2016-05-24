<?php $recurringEventTypes = array(
   '2' => t('Wöchentliche Wiederholung'),
   '3' => t('2-wöchentliche Wiederholung'),
   '4' => t('Monatliche Wiederholung'),
   '5' => t('2-monatliche Wiederholung')
  ); ?>

<header id="eventsPageHeader" class="pageHeader">
  <h2><?= $itemsCount; ?> Veranstaltungen/Events <a href="<?= base_path(); ?>events/rss" title="<?= t('Alle Events als RSS-Feed'); ?>"><img id="svg_logo" src="<?= base_path().path_to_theme(); ?>/img/rss.svg" /></a></h2>
  <p>Finde Workshops, Kreativwerkstätten, Märkte, Versammlungen und mehr.</p>
</header>

<div id="events" class="row" style="padding-top:280px;">

  <aside class="aae-sidebar large-3 columns right">

  <?php if(user_is_logged_in()) : ?>
   <a class="medium button hollow round larg-12 columns" href="<?= base_path(); ?>events/new"><?= t('+ Event hinzufügen'); ?></a><br />
  <?php else : ?>
   <a class="login_first medium button hollow round large-12 columns" href="<?= base_path(); ?>user/login" title="<?= t('Bitte zunächst einloggen.'); ?>"><?= t('+ Event hinzufügen'); ?> (<?= t('Login'); ?>)</a><br />
   <?php endif; ?>

  <div id="filter" class="large-12 columns">

   <div class="large-12 columns" id="removeFilter">
    <h4 class="left"><?= t('Filter'); ?></h4>
    <a class="small button right hide-for-medium" style="padding:4px 10px;margin-left:5px;" href="#" title="<?= t('Zeige Filter'); ?>" onclick="javascript:$('#filter .large-12').slideDown(400);">&#x25BE;</a>
    <a class="small secondary button right" style="padding:4px 10px;" href="<?= base_path(); ?>events/" title="<?= t('Alle Filter entfernen.'); ?>">X</a>
    <div class="divider"></div>
   </div>

   <div class="large-12 columns">

    <form id="filterForm" method="GET" action="<?= base_path(); ?>events">

     <label for="filterKeyword"><?= t('Schlagwort:'); ?></label>
     <input name="filterKeyword" id="filterKeywordInput" type="text" <?= (isset($this->filter['keyword']) ? 'value="'.$this->filter['keyword'].'"' : ''); ?>/>

     <label for="filterTags"><?= t('Tags:'); ?></label>
     <select name="filterTags[]" id="eventSpartenInput" multiple="multiple" class="tokenize">
     <?php foreach ($resultTags as $tag) : ?>
      <option value="<?= $tag->KID; ?>"<?= (in_array($tag->KID,$this->filter['tags']) ? ' selected="selected"' : ''); ?>><?= $tag->kategorie; ?></option>
     <?php endforeach; ?>
     </select>

   </div>
   <div class="large-12 columns">

     <label for="filterBezirke"><?= t('Bezirke:'); ?></label>
     <select name="filterBezirke[]" id="eventBezirkInput" multiple="multiple" class="tokenize">
     <?php foreach ($resultBezirke as $bezirk) : ?>
      <option value="<?= $bezirk->BID; ?>"<?= (in_array($bezirk->BID,$this->filter['bezirke']) ? ' selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
     <?php endforeach; ?>
   </select>

   </div>
   <div id="timespace" class="large-12 columns">

    <label><?= t('Zeitraum:'); ?></label>

    <div class="slider" data-slider>
     <span class="slider-handle sh-1" data-slider-handle role="slider" tabindex="1"></span>
     <span class="slider-fill" data-slider-fill></span>
     <span class="slider-handle sh-2" data-slider-handle role="slider" tabindex="1"></span>
     <input type="hidden">
     <input type="hidden">
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

   </form>
  </div>
  </aside>

  <div id="events_content" class="large-9 small-12 columns">

   <ul class="tabs" id="events-tabs" style="margin-bottom:22px;">
    <li class="tabs-title<?= ($this->getOldEvents || $this->hasFilters ? '' : ' is-active'); ?>"><a href="<?= base_path(); ?>events"<?= ($this->getOldEvents || $this->hasFilters ? '' : ' aria-selected="true"'); ?>>Demnächst</a></li>
    <li class="tabs-title<?= ($this->getOldEvents && !$this->hasFilters ? ' is-active' : ''); ?>"><a href="<?= base_path(); ?>events/old"<?= ($this->getOldEvents ? ' aria-selected="true"' : ''); ?>>Vergangene Events</a></li>
    <?php if (!empty($this->filters)) : ?><li class="tabs-title is-active"><a href="#" aria-selected="true"><?= t('Filterergebnisse'); ?> (<?= count($resultEvents); ?>)</a></li><?php endif; ?>
     <!--label>Darstellung:</label>//$_SERVER[REQUEST_URI];-->
    <ul id="presentationFilter" class="button-group round large-3 columns right">
     <li class="right"><a href="<?= base_path(); ?>events" name="timeline" class="small button <?php echo ($this->presentationMode !== 'calendar' ? 'active' : 'secondary'); ?>" title="<?= t('Darstellung als Timeline'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/ios-list-outline.svg" /></a></li>
     <li class="right"><a href="<?= base_path(); ?>events/?presentation=calendar" name="kalender" class="small button <?php echo ($this->presentationMode == 'calendar' ? 'active' : 'secondary'); ?>" title="<?= t('Darstellung als Kalender'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/ios-grid-view-outline.svg" /></a></li>
    </ul>

   </ul>

 <?php if (isset($resultKalender) && !empty($resultKalender)) : ?>

  <div id="aae_calendar">
   <?= $resultKalender; ?>
  </div>

 <?php elseif (is_array($resultEvents) && !empty($resultEvents)) : ?>
  <?php foreach($resultEvents as $key => $event): ?>
   <?php if ($event->start->format('m.Y') != $cur_month) : ?>
    <div class="large-12 columns"><h4><?= $this->monat_lang[$event->start->format('m')]; ?> <?= $event->start->format('Y'); ?></h4></div>
   <?php endif; $cur_month = $event->start->format('m.Y'); ?>

   <div class="large-6 columns small-6 columns aaeEvent<?= ($event->start->format('Y-m-d') == date('Y-m-d')) ? ' today' : ''; ?><?= (!empty($event->eventRecurringType) ? ' eventRecurres' : ''); ?>">

   <div class="date large-2 columns button secondary round"><?= $event->start->format('d'); ?><br /><?= $this->monat_short[$event->start->format('m')]; ?></div>
   <div class="content large-9 columns">
   <header<?= (!empty($event->eventRecurringType) ? ' title="'.$recurringEventTypes[$event->eventRecurringType].'"' : ''); ?>>
    <p><a style="line-height:1.6em;" href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>"><strong><?= $event->name; ?></strong></a><br />
    <span><?= ($event->start->format('Y-m-d') == date('Y-m-d')) ? '<a href="#">Heute,</a> ' : ''; ?><?php if($event->start->format('H:i') !== '00:00') echo $event->start->format('H:i'); ?><?php if($event->ende->format('H:i') !== '00:00') echo ' - '. $event->ende->format('H:i'); ?><?= (!empty($event->eventRecurringType) ? '  '.$recurringEventTypes[$event->eventRecurringType] : ''); ?></span></p>
    <p>
     <?php foreach($event->tags as $tag) : ?>
     <a class="tag" href="<?= base_path(); ?>events/?filterTags[]=<?= $tag->KID; ?>" rel="nofollow">#<?= $tag->kategorie; ?></a>
     <?php endforeach; ?>
    </p>
   </header>
   <?php if (!empty($event->kurzbeschreibung) && (empty($event->eventRecurringType))) : ?>
    <div class="divider"></div>
    <div class="event-content">
      <?php $numwords = 30; preg_match("/(\S+\s*){0,$numwords}/", $event->kurzbeschreibung, $regs); ?>
      <div class="eventDesc"><p><?= strip_tags(trim($regs[0])); ?> <a class="weiterlesen" href="<?= base_path().'eventprofil/'.$event->EID; ?>">...<?= t('weiterlesen'); ?></a></p></div>
    </div>
   <?php endif; ?>
   </div>

   <div class="akteurData large-10 columns">
    <p><a href="<?= base_path().'akteurprofil/'.$event->akteur->AID; ?>" title="<?= t('Akteurprofil von !username', array('!username' => $event->akteur->name)); ?>"><img src="<?= $event->akteur->bild; ?>" /><?= $event->akteur->name; ?></a></p>
   </div>

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
      <a class="tagc-<?= (($tag->count / 2) >= 5 ? '5' : ceil($tag->count / 2)); ?> tag" href="<?= base_path(); ?>events/?filterTags[]=<?= $tag->KID; ?>" rel="nofollow">#<?= $tag->kategorie; ?></a>
    <?php endforeach; ?>
    </aside>

  </div>
 </div>
