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

  </div>

 <section class="section" id="projects">

  <h1>Neueste <strong>Projekte</strong></h1>

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

   <div class="large-3 large-offset-1 columns event">
    <a href="#"><button class="button blue date">08<br />Sept</button></a>
    <a href="#"><h4>Cosplay Workshop</h4></a>
    <aside><a href="#">
     <img src="img/location.svg" style="width: 15px;" />UT Connewitz <br/>
     <img src="img/clock.svg" style="width: 15px;" /><strong>8:00</strong> - <strong>12:30</strong></p>
    </a></aside>
   </div>

   <div class="large-3 large-offset-1 columns event">
    <a href="#"><button class="button blue date">08<br />Sept</button></a>
    <a href="#"><h4>Cosplay Workshop</h4></a>
    <aside><a href="#">
     <img src="img/location.svg" style="width: 15px;" />UT Connewitz <br/>
     <img src="img/clock.svg" style="width: 15px;" /><strong>8:00</strong> - <strong>12:30</strong></p>
    </a></aside>
   </div>

 </div> -->

 </section>

 <header class="section" id="header">

  <!--<div id="intro" class="row">
   <h1>Den <strong>Leipziger Osten</strong> neu entdecken.</h1>
   <div id="introtext">
   <p>Deine Plattform f&uuml;r den ganzen Stadtteil: Lerne <strong>kreative Projekte</strong> aus Deiner Umgebung kennen & erfahre, wann und wo sich etwas in Deinem Bezirk bewegt!</p>
   <p>Kostenlos, offen, lokal.</p>
   </div>
   <a href="#pageProjects"><button class="button radius">Zu den <strong>Projekten</strong></button></a>
   <a href="#"><button class="button radius secondary"><strong>Veranstaltungen</strong></button></a>
 </div> -->

  <div id="map"></div>
 </header>

 <section class="section" id="bottom">

  <div class="row" id="teaser">
	 <h1><strong>45</strong> Initiativen, <strong>213</strong> Veranstaltungen, <strong>eine</strong> Plattform.</h1>

   <p>Mach' mit! Stelle jetzt Dein Projekt ein oder mische als registrierter Nutzer mit:</p>

   <a href="#"><button class="button radius">Registrieren</button></a>
   <a href="#"><button class="button radius secondary">Anmelden</button></a>

  </div>
 </section>

 <section class="section" id="footer">
 <?php include_once('footer.tpl.php'); ?>
 </section>

</div><!--/#fullpage -->
