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

?>

<div id="fullpage">

  <div class="section" id="slideSection">

  <?php

  // Let's build a slider :D

  include 'slider_settings.php';

  foreach($sliders as $id => $content ): ?>

  <?php
  $blueButton = ($content['blueButton']['text'] ? '<a href="'.$content['blueButton']['link'].'"><button class="button radius transparent">'.$content['blueButton']['text'].'</button></a>' : '' );
  //$whiteButton = ($content['whiteButton']['text'] ? '<a href="'.$content['whiteButton']['link'].'"><button class="button radius secondary">'.$content['whiteButton']['text'].'</button></a>' : '');
  ?>

  <div class="slide <?= ($content['whiteText'] == true ? 'whiteText' : ''); ?>" id="slide<?= $id; ?>" style="background-image:url(<?= base_path().path_to_theme().'/img/'.$content['image']; ?>)";>
    <h1><?= $content['headline']; ?></h1>
    <?= $content['description']; ?>
    <?= $blueButton; ?>
  </div>
  <?php endforeach; ?>

  </div>

  <section class="section" id="journal">
   <?php print render($page['blog']); ?>
  </section>

  <section class="section" id="projects-events">

    <div class="row">
     <div class="aaeHeadline">
      <h1><span>&nbsp;<strong>Neueste Veranstaltungen</strong></span></h1>
      <a href="<?= base_path(); ?>events/rss" id="rss" class="small button" title="Alle Events als RSS-Feed abonnieren"><img id="svg_logo" src="/sites/all/themes/aae_theme/img/rss.svg"></a>
      <a href="<?= base_path(); ?>events" id="allevents" class="small button frontpage">Alle Events</a>
     </div>
     <div>
      <?php
      // Lade "letzte Events"-Block
      foreach ($blocks->print_letzte_events() as $event) : ?>
      <div>
        <div class="large-4 columns large3-events">
        <a href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>">
          <button class="date"><?= $event->start->format('d'); ?><br/><?= $monat_short[$event->start->format('m')]; ?></button>
        </a>
        <a href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>">
          <div class="events-align event">
            <h4><?= $event->name; ?></h4>
            <div class="divider"></div>
            <aside><!--<img src="<?= base_path().path_to_theme(); ?>/img/clock.svg" />--><?= $event->start->format('H:i'); ?><?php if ($event->ende->format('H:i') != '00:00') :?> - <?= $event->ende->format('H:i'); ?><?php endif; ?></aside>
          </div>
        </a>
       </div>
      </div>
      <?php endforeach; ?>
    </div>
    </div>

  </section>

  <section class="section" id="projects-akteure">
    <div class="row">
     <div class="aaeHeadline">
      <h1><span>&nbsp;<strong>Neueste Akteure</strong>&nbsp;</span></h1>
      <a href="<?= base_path(); ?>akteure" id="allakteure" class="small button frontpage">Alle Akteure</a>
     </div>

      <?php
      // Lade "Meine Akteure"-Block

      foreach ($blocks->print_letzte_akteure() as $akteur) : ?>
      <a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>">
       <div class="large-3 small-5 columns pcard">
        <header<?php if($akteur->bild != '') echo ' style="background-image:url('.$akteur->bild.');"'; ?>>
         </header>
         <section>
          <h3><?= $akteur->name; ?></h3>
          <?php if (!empty($akteur->bezirk)) : ?>
            <div class="divider"></div>
            <p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p>
          <?php endif; ?>
          <!--<div class="divider"></div>
          <p class="pdescription"><?= substr($akteur->beschreibung, 0, 120); ?>...</p> -->
         </section>
        </div>
      </a>
      <?php endforeach; ?>

    </div> <!--#row-->

  </section> <!--#akteure-->

  <?php include_once('footer.tpl.php'); ?>

</div><!--/#fullpage -->
