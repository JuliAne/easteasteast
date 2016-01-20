<?php
 $monat = array(
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
 ?>

<?php if (!empty($page['sidebar_first'])): ?>

  <aside class="col-sm-3" role="complementary">
    <?php print render($page['sidebar_first']); ?>
  </aside>  <!-- /#sidebar-first -->

<?php endif; ?>

<?php include_once('header.tpl.php'); ?>

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

  <!--<div class="slide">
    <div id="map"></div>
  </div>-->

  </div>

  <section class="section" id="journal">
    <?php print render($page['blog']); ?>
  </section>

  <section class="section" id="projects-events">

    <div class="row">
     <div class="aaeHeadline">
      <h1><span>&nbsp;<strong>N&auml;chste Veranstaltungen</strong>&nbsp;</span></h1>
      <a href="<?= base_path(); ?>events/rss" id="rss" class="small button" title="Alle Events als RSS-Feed abonnieren"><img id="svg_logo" src="/sites/all/themes/aae_theme/img/rss.svg"></a>
      <a href="<?= base_path(); ?>events" id="allevents" class="small button frontpage">Alle Events</a>
     </div>
     <div>
      <?php
      // Lade "letzte Events"-Block
      foreach ($blocks->print_letzte_events() as $event) : ?>

      <?php $exploded = explode("-", $event->start); ?>

      <div>
        <div class="large-3 columns large3-events">
        <a href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>">
          <button class="date"><?= $exploded[0]; ?><br/><?= $monat[$exploded[1]]; ?></button>
        </a>
        <a href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>">
          <div class="events-align event">
            <h4><?= $event->name; ?></h4>
            <div class="divider"></div>
            <aside><img src="<?= base_path().path_to_theme(); ?>/img/clock.svg" /><?= $event->zeit_von; ?><?php if (!empty($event->zeit_bis)) :?> - <?= $event->zeit_bis; ?><?php endif; ?></aside>
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

      include_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';

      $blocks = new aae_blocks();

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
