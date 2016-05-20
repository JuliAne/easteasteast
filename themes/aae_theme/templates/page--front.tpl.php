<?php

 $monat_short = array(
  '01' => 'Jan',
  '02' => 'Feb',
  '03' => 'Mär',
  '04' => 'Apr',
  '05' => 'Mai',
  '06' => 'Jun',
  '07' => 'Jul',
  '08' => 'Sep',
  '09' => 'Aug',
  '10' => 'Okt',
  '11' => 'Nov',
  '12' => 'Dez',
);

 include_once('header.tpl.php');
 $path = drupal_get_path('module', 'aae_data');
 include_once $path . '/aae_blocks.php';
 $blocks = new aae_blocks();
 include_once 'slider_settings.php';
 $counts = $blocks->count_projects_events();

?>

<div class="section" id="imgSlideSection">

<?php foreach($sliders as $id => $content ): ?>
<div class="slide" id="slide<?= $id; ?>" style="background-image:url(<?= base_path().path_to_theme().'/img/'.$content['image']; ?>)";></div>
<?php endforeach; ?>

</div>

<div id="fullpage">

  <div class="section" id="slideSection">

  <div class="slide whiteText" id="slide0">
    <h1>Den Leipziger Osten entdecken.</h1>
    <p>Deine Stadtteilplattform: Erfahre mehr über Vereine, Initiativen und Akteure aus Deiner Umgebung,</p>
    <p>werde Teil der Community und registriere dich.</p>
    <p class="slogan"><strong>Offen. Lokal. Vernetzt.</strong></p>
    <a href="<?= base_path(); ?>faq"><button class="button radius transparent">Mehr erfahren...</button></a>
    <a href="<?= base_path(); ?>user/login"><button id="homeLoginBtn" class="button radius transparent hollow">Einloggen</button></a>
  </div>

  <div class="slide whiteText" id="slide1">
    <h1><strong><?= $counts['akteure']; ?></strong> Akteure. <strong><?= $counts['events']; ?></strong> Events.<br /> Eine Plattform.</h1>
    <p>Jetzt anmelden, Akteur werden, Veranstaltungen einstellen und mitmischen.</p>
    <a href="<?= base_path(); ?>user/register"><button class="button radius transparent">Jetzt registrieren.</button></a>
  </div>

  <div class="slide whiteText" id="slide2">
    <h1>Der Leipziger Osten</h1>
    <p>vom „Grafischen Viertel” über Neustadt-Neuschönefeld, Volkmarsdorf, Schönefeld bis Sellerhausen-Stünz:</p>
    <p class="slogan"><strong>vernetzt und kooperativ für ein buntes Leipzig</strong></p>
    <a href="<?= base_path(); ?>leipziger-osten"><button class="button radius transparent">Über den Osten.</button></a>
  </div>

  <div id="hitMeScrollMe" title="<?= t('Weiterspringen'); ?>">></div>

 </div>

  <section class="section" id="journal">

    <div class="row">
     <div class="aaeHeadline">
      <h1><span>&nbsp;<strong><?= t('Digitales Stadtteiljournal'); ?></strong>&nbsp;</span></h1>
      <a href="<?= base_path(); ?>journal" id="allakteure" class="small button frontpage"><?= t('Zum Journal'); ?></a>
     </div>
   </div>

   <?php print render($page['journal_latest_posts']); ?>

  </section>

  <section class="section" id="projects-events">

    <div class="row">
     <div class="aaeHeadline">
      <h1><span><strong><?= t('Neueste Veranstaltungen'); ?></strong></span></h1>
      <a href="<?= base_path(); ?>events/rss" id="rss" class="small button" title="<?= t('Alle Events als RSS-Feed abonnieren'); ?>"><img id="svg_logo" src="<?= base_path().path_to_theme().'/img/rss.svg'; ?>"></a>
      <a href="<?= base_path(); ?>events" id="allevents" class="small button frontpage"><?= t('Alle Events'); ?></a>
     </div>
    </div>
    <div class="row">
      <?php
      // Lade "letzte Events"-Block
      foreach ($blocks->print_letzte_events() as $event) : ?>
        <div class="large-6 small-6 columns large3-events">
        <a href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>">
          <button class="date"><?= $event->start->format('d'); ?><br/><?= $monat_short[$event->start->format('m')]; ?></button>
        </a>
        <a href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>">
          <div class="events-align event">
            <h4><?= $event->name; ?></h4>
            <div class="divider"></div>
            <aside><!--<img src="<?= base_path().path_to_theme(); ?>/img/clock.svg" />--><?= $event->start->format('H:i'); ?><?php if ($event->ende->format('H:i') != '00:00') :?> - <?= $event->ende->format('H:i'); ?><?php endif; ?></aside>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

  </section>

  <section class="section" id="projects-akteure">
    <div class="row">
     <div class="aaeHeadline">
      <h1><span>&nbsp;<strong><?= t('Neueste Akteure'); ?></strong>&nbsp;</span></h1>
      <a href="<?= base_path(); ?>akteure" id="allakteure" class="small button frontpage"><?= t('Alle Akteure'); ?></a>
     </div>

      <?php
      // Lade "Meine Akteure"-Block

      foreach ($blocks->print_letzte_akteure(8) as $count => $akteur) : ?>
      <a href="<?= base_path().'akteurprofil/'.$akteur->AID; ?>" title="<?= t('Akteurprofil besuchen'); ?>">
      <div class="large-3 small-5 columns pcard<?= ($count >= 4 ? ' show-for-medium':''); ?>">
       <header <?= (!empty($akteur->bild) ? 'style="background-image:url('.$akteur->bild.');"' : ''); ?><?= ($akteur->renderSmallName ? ' class="renderSmallName"' : ''); ?>>
        </header>
         <h3><?= $akteur->name; ?>
             <?php if (!empty($akteur->bezirk)) : ?><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p><?php endif; ?></h3>
        <section style="display:none;">
          <?php if (!empty($akteur->bezirk)) : ?><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p><?php endif; ?>
        </section>
       </div>
       </a>
      <?php endforeach; ?>

    </div> <!--#row-->

  </section> <!--#akteure-->

  <?php include_once('footer.tpl.php'); ?>

</div><!--/#fullpage -->
