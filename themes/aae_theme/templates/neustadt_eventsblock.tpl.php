<?php if (isset($resultKalender) && !empty($resultKalender)) : ?>

  <div id="aae_calendar">
   <?= $resultKalender; ?>
  </div>

 <?php elseif (is_array($resultEvents) && !empty($resultEvents)) : ?>
  
  <?php foreach($resultEvents as $key => $event): ?>
   <?php if ($event->start->format('d') != $cur_day) : ?>
    <div class="large-12 columns" style="padding:10px 0;"><h4><?= $this->dayNames[date('N', $event->start->getTimestamp())-1]; ?>, <?= $event->start->format('d'); ?>. <?= $this->monat_short[$event->start->format('m')]; ?></h4></div>
   <?php endif; $cur_day = $event->start->format('d'); ?>
   <div class="large-6 columns small-6 columns aaeEvent<?= ($event->start->format('Y-m-d') == date('Y-m-d')) ? ' today' : ''; ?>">

   <div class="date large-2 columns button secondary round">Ab<br /><?= $event->start->format('H:i')  ; ?></div>
   <div class="content large-9 columns">
   <header<?= (!empty($event->eventRecurringType) ? ' title="'.$recurringEventTypes[$event->eventRecurringType].'"' : ''); ?>>
    <h3><a href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>"><?= $event->name; ?></a></h3>
    <p><span><?= ($event->start->format('Y-m-d') == date('Y-m-d')) ? '<a href="#">Heute,</a> ' : ''; ?><?php if($event->start->format('H:i') !== '00:00') echo $event->start->format('H:i'); ?><?php if($event->ende->format('H:i') !== '00:00') echo ' - '. $event->ende->format('H:i'); ?>
    <?php $bezirk = trim(preg_replace("/\(\w+\)/", "", $event->adresse->bezirksname)); ?>
    | <?= $event->adresse->strasse.' '.$event->adresse->nr. ' | '. $bezirk; ?></span></p>
    <p>
     <?php foreach($event->tags as $tag) : ?>
     <a class="tag" href="<?= base_path(); ?>events/?filterTags[]=<?= $tag->KID; ?>" rel="nofollow">#<?= $tag->kategorie; ?></a>
     <?php endforeach; ?>
    </p>
   </header>
   <?php if (!empty($event->kurzbeschreibung)) : ?>
    <div class="divider"></div>
    <div class="event-content">
      <?php $numwords = 36; preg_match("/(\S+\s*){0,$numwords}/", $event->kurzbeschreibung, $regs); ?>
      <div class="eventDesc"><p><?= strip_tags(trim($regs[0])); ?> <a class="weiterlesen" href="<?= base_path().'eventprofil/'.$event->EID; ?>">...<?= t('Zum Event'); ?></a></p></div>
    </div>
   <?php endif; ?>
   </div>

  </div>
 <?php endforeach; else : ?>

   <p style="text-align:center;"><?= t('Es wurden noch keine Events angelegt...'); ?></p>
   <p style="text-align:center;"><a href="<?= base_path(); ?>events/new">Festivalevent hinzfügen</a></p>

 <?php endif; ?>