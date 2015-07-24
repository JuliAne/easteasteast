<script src="<?= base_path().drupal_get_path('module', 'aae_data'); ?>/LOdata.js"></script>

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
    <p><?= $content['description']; ?></p>

    <?= $blueButton; ?>
  </div>
  <?php endforeach; ?>

  <div class="slide">
    <div id="map"></div>
  </div>

  </div>

 <section class="section" id="projects">

  <h1>Neueste <strong>Projekte</strong></h1>

  <div class="row">

  <?php
  // Lade "letzte Akteure"-Block

  require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';

  foreach (block_aae_print_letzte_akteure() as $akteur) : ?>

   <div class="large-3 large-offset-1 columns pcard">
    <header>
      <h3><a href="#"><?= $akteur->name; ?></a></h3>
      <!--<img title="Barrierefrei" class="barrierefrei" src="img/wheelchair.svg" />-->
     </header>
     <section>
      <p><!--<strong>Reudnitz</strong>--><?= $akteur->kurzbeschreibung; ?></p>
      <p><a href="?q=Akteurprofil/<?= $akteur->AID; ?>">Zum Projekt...</a></p>
     </section>
     <footer>
      <a href="#" title="Hier erscheint bei Klick eine Minimap inkl. Strassenangabe"><img src="<?= base_path().path_to_theme(); ?>/img/location.svg" /></a>
      <a href="#" title="Weiterleitung zu Terminen dieses Projektes"><img class="gimmeborder" src="<?= base_path().path_to_theme(); ?>/img/calendar.svg" /></a>
      <a href="<?= base_path(); ?>q=Akteurprofil/<?= $akteur->AID; ?>"><button class="button blue">&gt;</button></a>
     </footer>
    </div>

  <?php endforeach; ?>

  </div>

  <h1>N&auml;chste <strong>Veranstaltungen</strong></h1>

  <div class="row">

  <?php
  // Lade "letzte Events"-Block

  foreach (block_aae_print_letzte_events() as $event) : ?>

   <div class="large-3 large-offset-1 columns event">
    <a href="#"><button class="button blue date">08<br />Sept</button></a>
    <a href="#"><h4><?= $event->name; ?></h4></a>
    <aside><a href="<?= base_path(); ?>?q=Eventprofil/<?= $event->EID; ?>">
     <img src="<?= base_path().path_to_theme(); ?>/img/location.svg" /><?= $event->veranstalter; ?> <br/>
     <img src="<?= base_path().path_to_theme(); ?>/img/clock.svg" /><strong>BEGINN</strong> - <strong>ENDE</strong></p>
    </a></aside>
   </div>

 <?php endforeach; ?>

  </div>

 </section>

 <section class="section" id="journal">
   <h2>Journal</h2>

   <?php
   // Lade "Letzte Blog-Artikel"-Block

   /*foreach (block_aae_print_letzte_artikel() as $artikel) : ?>

     <div class="row artikel">
      <div class="large-2 columns"><img style="width: 50px; border-radius: 25px;" src="pcard_bg.jpg" /></div>

      <div class="large-9 columns large-offset-1">
       <a href="<?= base_path(); ?>?q=node/<?= $artikel->nid; ?>"><h3><?= $artikel->title; ?></h3></a>

       <p>Lorem ipsum sed dolor sit amet Lorem ipsum sed dolor sit amet Lorem ipsum sed dolor sit amet Lorem ipsum sed dolor sit amet Lorem ipsum sed dolor sit amet Lorem ipsum sed dolor sit...</p>
       <p>Von <i>Matthias</i> am 23.08.2015. <a href="#">2 Kommentare</a>
      </div>
    </div>

    <?php endforeach; */ ?>

   <?php print render($page['content']); ?>

 </section>

<?php include_once('footer.tpl.php'); ?>

</div><!--/#fullpage -->
