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

  <section class="section" id="projects-akteure">

    <h1>Neueste <strong>Akteure</strong></h1>

    <div class="row">

      <?php
      // Lade "Meine Akteure"-Block

      include_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';

      $blocks = new aae_blocks();

      foreach ($blocks->print_letzte_akteure() as $akteur) : ?>

       <div class="large-3 large-offset-1 columns pcard">
        <header<?php if($akteur->bild != '') echo ' style="background:url('.$akteur->bild.');"'; ?>>
          <h3><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>"><?= $akteur->name; ?></a></h3>
          <!--<img title="Barrierefrei" class="barrierefrei" src="img/wheelchair.svg" />-->
         </header>
         <section>
          <p><!--<strong>Reudnitz</strong>--><?= substr($akteur->beschreibung, 0, 120); ?>...</p>
         </section>
         <footer>
          <a href="#" title="Hier erscheint bei Klick eine Minimap inkl. Strassenangabe"><img src="<?= base_path().path_to_theme(); ?>/img/location.svg" /></a>
          <a href="#" title="Weiterleitung zu Terminen dieses Projektes"><img class="gimmeborder" src="<?= base_path().path_to_theme(); ?>/img/calendar.svg" /></a>
          <a href="<?= base_path(); ?>Akteurprofil/<?= $akteur->AID; ?>" title="Profil besuchen"><button class="button blue">&gt;</button></a>
         </footer>
        </div>

      <?php endforeach; ?>

    </div> <!--#row-->

  </section> <!--#akteure-->

  <?php include_once('footer.tpl.php'); ?>

</div><!--/#fullpage -->
