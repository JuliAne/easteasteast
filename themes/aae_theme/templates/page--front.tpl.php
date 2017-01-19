<?php

 include_once('header.tpl.php');

 // TODO: Beautify the next lines...
 $path = drupal_get_path('module', 'aae_data');
 include_once $path . '/aae_blocks.php';
 $blocks = new Drupal\AaeData\aae_blocks();
 $counts = $blocks->count_projects_events();
 $aaeHelper = new Drupal\AaeData\aae_data_helper;
 $resultBezirke = $aaeHelper->getAllBezirke();

?>
<div id="fullpage">

 <section class="section" id="startSection">

  <div class="slide whiteText" id="slide0">
    <h1>Den Leipziger Osten entdecken.</h1>
    <p>Deine Stadtteilplattform: Erfahre mehr über Vereine, Initiativen und Akteure aus Deiner Umgebung,</p>
    <p>werde Teil der Community und registriere dich.</p>
    <p class="slogan"><strong>Offen. Lokal. Vernetzt.</strong></p>
    <a href="<?= base_path(); ?>leipziger-ecken-das-projekt"><button class="button radius transparent"><?= t('Mehr erfahren...'); ?></button></a>
    <a href="<?= base_path(); ?>nutzer/login"><button id="homeLoginBtn" class="button radius transparent hollow"><?= t('Einloggen'); ?></button></a>
  </div>

 </section>

  <section class="section" id="aboutEcken">

    <div class="row">
     <div class="aaeHeadline">
      <h1 id="dieEckenLink" class="active"><?= t('Die Ecken'); ?></h1>
      <h1 id="journalLink"><?= t('Digitales Stadtteiljournal'); ?></h1>
      <a href="https://leipziger-ecken.de/rss.xml" id="rss" class="small button hide-for-small-only right" title="<?= t('Beiträge als RSS'); ?>"><img id="svg_logo" src="/sites/all/themes/aae_theme/img/rss.svg"></a>
      <a href="<?= base_path(); ?>journal" id="fullJournalLink" class="small button hollow transparent right"><?= t('Zum Journal'); ?></a>
     </div>
   </div>

   <div id="aboutEckenWelcome" class="slide">

   <div class="row">

    <div class="large-4 columns" id="mainTable">
     <h3>Vernetzt und kooperativ für ein buntes Leipzig.</h3>
     <p>Die <strong>Leipziger Ecken</strong> sind ein einzigartiges Web-Format für zivile Akteure aus den Bezirken des Leipziger Ostens.</p>
     <p>Mit unseren freien Tools möchten wir gelebtes Engagement, Kreativität und Schaffenskultur für unsere Nachbarn und Gäste sichtbar machen und Zugangspunkte schaffen.</p>
    </div>

    <div class="large-7 right hide-for-small-only columns">
    
    <div class="large-12 infoTable columns">
     <h3>Hereinspaziert...</h3>
     <p>Hier können sich Initiativen, Vereine oder Künstler einer breiten Öffentlichkeit präsentieren und zu Events einladen. Bestehende digitale, soziale Kommunikationsmittel können problemlos in das Profil integriert werden - das spart Aufwand.</p><br /><br /><p>Ut accusamus quia animi culpa quia voluptatem… Rem quam est quae est. </p>
    </div>

    <div class="large-12 infoTable columns">
     <h3>...Und mitgemacht!</h3>
     <p>Wir stehen erst am Anfang, denn in den nächsten Monaten soll dies ein Ort des Netzwerkens und Austausches werden, auch für privatpersonen.</p>
    </div>

    </div>

    <div id="bottomJournalLink" class="large-4 columns hide-on-small-only left">
     <a href="#">&#62; <?= t('Zur Journalvorschau'); ?></a>
    </div>

   </div>
  
  <div class="slide">
   <div class="row">
   <?php print render($page['journal_latest_posts']); ?>
   </div>
  </div>

  </section>

  <section class="section" id="projects-events">

    <div class="row">
     <div class="aaeHeadline large-2 columns right">
      <h1><span><strong><?= t('Nächste Events'); ?></strong></span></h1>
      <a href="<?= base_path(); ?>events/rss" id="rss" class="small button hide-for-small-only" title="<?= t('Alle Events als RSS-Feed abonnieren'); ?>"><img id="svg_logo" src="/sites/all/themes/aae_theme/img/rss.svg"></a>
      <a href="<?= base_path(); ?>events" class="small button transparent hollow"><?= t('Alle Events'); ?></a>
     </div>

    <div class="large-10 columns">
      <?php
      // Load "next Events"-block

      foreach ($blocks->print_next_events() as $event) :
        $time = ($event->start->format('H:i') != '00:00' ? $event->start->format('H:i') : '').($event->ende->format('H:i') != '00:00' ? ' - '.$event->ende->format('H:i') : ''); ?>
        <div class="large-6 columns eventCard<?= (empty($time) ? ' noTime' : ''); ?>">
        <a href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>" title="<?= t('Event aufrufen'); ?>">
         <button class="date"><?= $event->start->format('d'); ?><br/><?= $aaeHelper->monat_short[$event->start->format('m')]; ?></button>
        </a>
        <a href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>" title="<?= t('Event aufrufen'); ?>">
         <div class="events-align event">
          <h4><?= $event->name; ?></h4>
          <?php if (!empty($time)) : ?><div class="divider hide-for-small-only"></div><aside class="hide-for-small-only"><?= $time; ?></aside><?php endif; ?>
         </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
   </div><!-- /.row -->

  </section>

  <section class="section" id="projects-akteure">
    <div class="row">

     <div class="aaeHeadline large-2 columns">
      <h1><span><strong><?= t('Neueste Akteure'); ?></strong></span></h1>
      <a href="<?= base_path(); ?>akteure" class="small button hollow transparent"><?= t('Alle Akteure'); ?></a>
      <form id="triggerFilterBezirke" method="get" action="<?= base_path(); ?>akteure">    
       <select name="filterBezirke[]" title="<?= t('Nach Bezirk filtern'); ?>" onchange="triggerFormClick();">
        <option value="" selected="selected"><?= t('Nach Bezirk filtern'); ?></option>
        <?php foreach ($resultBezirke as $bezirk) : ?>
        <option value="<?= $bezirk->BID; ?>"><?= trim(preg_replace("/\(\w+\)/", "", $bezirk->bezirksname)); ?></option>
        <?php endforeach; ?>
       </select>
      </form>
     </div>
     
     <div class="frame">
     <div class="slidee large-10 columns">
      <?php
      // Load "Latest Akteure"-block

      foreach ($blocks->print_letzte_akteure(8) as $count => $akteur) : ?>
      <a href="<?= base_path().'akteurprofil/'.$akteur->AID; ?>" title="<?= t('Akteurprofil von !username', array('!username' => $akteur->name)); ?>">
      <div class="large-3 small-5 columns pcard<?= ($count >= 4 ? ' show-for-medium':''); ?>">
       <header <?= (!empty($akteur->bild) ? 'style="background-image:url('.$akteur->bild.');"' : ''); ?><?= ($akteur->renderSmallName ? ' class="renderSmallName"' : ''); ?>></header>
         <h3><p class="akteurname"><?= $akteur->name; ?></p>
         <?php if (!empty($akteur->bezirk)) : ?><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p><?php endif; ?></h3>
        <section style="display:none;">
         <?php if (!empty($akteur->bezirk)) : ?><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p><?php endif; ?>
        </section>
       </div>
       </a>
      <?php endforeach; ?>
     </div>
     </div>

    </div><!--#row-->

  </section><!--#akteure-->

  <?php include_once('footer.tpl.php'); ?>

</div><!--/#fullpage -->
