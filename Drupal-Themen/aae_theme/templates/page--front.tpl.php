<script src="<?= base_path().path_to_theme(); ?>/LOdata.js"></script>

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
  $blueButton = ($content['blueButton']['text'] ? '<a href="'.$content['blueButton']['link'].'"><button class="button radius">'.$content['blueButton']['text'].'</button></a>' : '' );
  $whiteButton = ($content['whiteButton']['text'] ? '<a href="'.$content['whiteButton']['link'].'"><button class="button radius secondary">'.$content['whiteButton']['text'].'</button></a>' : '');
  ?>

  <div class="slide <?= ($content['whiteText'] == true ? 'whiteText' : ''); ?>" id="slide<?= $id; ?>" style="background-image:url(<?= base_path().path_to_theme().'/img/'.$content['image']; ?>)";>
    <h1><?= $content['headline']; ?></h1>
    <p><?= $content['description']; ?></p>

    <?= $blueButton.$whiteButton; ?>
  </div>
  <?php endforeach; ?>

  <div class="slide">
    <div id="map"></div>
  </div>

  </div>

 <section class="section" id="projects">

  <h1>Neueste <strong>Projekte</strong></h1>

  <?php
  // Lade "letzte Akteure"-Block

  require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/block_aae_letzte_akteure.php';

  foreach (block_aae_print_letzte_akteure() as $akteur) : ?>
  <?php //print_r($akteur); ?>

  <div class="row">
   <div class="large-3 large-offset-1 columns pcard">
    <header>
      <h3><a href="#"><?= $akteur->name; ?></a></h3>
      <!--<img title="Barrierefrei" class="barrierefrei" src="img/wheelchair.svg" />-->
     </header>
     <section>
      <p><!--<strong>Reudnitz</strong>--><?= $akteur->kurzbeschreibung; ?><a href="?q=Akteurprofil/<?= $akteur->AID; ?>">Zum Projekt...</a></p>
     </section>
     <footer>
      <a href="#" title="Hier erscheint bei Klick eine Minimap inkl. Strassenangabe"><img src="<?= base_path().path_to_theme(); ?>/img/location.svg" /></a>
      <a href="#" title="Weiterleitung zu Terminen dieses Projektes"><img class="gimmeborder" src="<?= base_path().path_to_theme(); ?>/img/calendar.svg" /></a>
      <button class="button blue"><a href="?q=Akteurprofil/<?= $akteur->AID; ?>">&gt;</a></button>
     </footer>
    </div>

  <?php endforeach; ?>

	<div class="row">
    <?php print render($page['content']); ?>
  </div>

  <!--<h1>N&auml;chste <strong>Veranstaltungen</strong></h1>

  <div class="row">

   <div class="large-3 large-offset-1 columns event">
    <a href="#"><button class="button blue date">08<br />Sept</button></a>
    <a href="#"><h4>Cosplay Workshop</h4></a>
    <aside><a href="#">
     <img src="img/location.svg" />UT Connewitz <br/>
     <img src="img/clock.svg" /><strong>8:00</strong> - <strong>12:30</strong></p>
    </a></aside>
   </div>

 </div> -->

 </section>

 <!--<section class="section" id="bottom">

  <div class="row" id="teaser">
	 <h1><strong>45</strong> Initiativen, <strong>213</strong> Veranstaltungen, <strong>eine</strong> Plattform.</h1>

   <p>Mach' mit! Stelle jetzt Dein Projekt ein oder mische als registrierter Nutzer mit:</p>

   <a href="#"><button class="button radius">Registrieren</button></a>
   <a href="#"><button class="button radius secondary">Anmelden</button></a>

  </div>
</section> -->

 <section class="section" id="footer">
 <?php include_once('footer.tpl.php'); ?>
 </section>

</div><!--/#fullpage -->
