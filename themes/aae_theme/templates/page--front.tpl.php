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

    <h1><span>&nbsp;<strong>N&auml;chste Veranstaltungen</strong>&nbsp;</span></h1>
    <br/>

    <div class="row">
    <div>
      <?php
      // Lade "letzte Events"-Block
      foreach ($blocks->print_letzte_events() as $event) : ?>

      <?php $exploded = explode("-", $event->start); ?>

      <div>
       <div class="large-3 columns large3-events"> 
       <button class="date"><?= $event->monat; ?>.<br/><?= $exploded[1]; ?>.</button>
        <div class="events-align event">
        <a href="#"><h4><?= $event->name; ?></h4></a>
        <div class="divider"></div>
        <aside><a href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>">
         <img src="<?= base_path().path_to_theme(); ?>/img/clock.svg" /><strong><?= $event->zeit_von; ?></strong><?php if (!empty($event->zeit_bis)) :?> - <strong><?= $event->zeit_bis; ?></strong><?php endif; ?></p>
        </a></aside>
        </div>
       </div>
      </div> 
      <?php endforeach; ?>
    </div>
    <div style="clear:both">
    <a href="<?= base_path(); ?>events" class="large button hollow frontpage">Alle Events</a>
    </div>

    </div>

  </section>

  <section class="section" id="projects-akteure">

    <h1><span>&nbsp;<strong>Neueste Akteure</strong>&nbsp;</span></h1>
    <br/>
    <div class="row">

      <?php
      // Lade "Meine Akteure"-Block

      include_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';

      $blocks = new aae_blocks();

      foreach ($blocks->print_letzte_akteure() as $akteur) : ?>

       <div class="large-3 large-offset-1 columns pcard">
        <header<?php if($akteur->bild != '') echo ' style="background-image:url('.$akteur->bild.');"'; ?>>
          <h3><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>"><?= $akteur->name; ?></a></h3>
         </header>
         <section>
          <?php if (!empty($akteur->bezirk)) : ?><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $akteur->bezirk; ?></p><?php endif; ?>
          <div class="divider"></div>
          <p><?= substr($akteur->beschreibung, 0, 120); ?>...</p>
         </section>
        </div>

      <?php endforeach; ?>

      <a href="<?= base_path(); ?>akteure" class="large button hollow frontpage">Alle Akteure</a>

    </div> <!--#row-->

  </section> <!--#akteure-->

  <?php include_once('footer.tpl.php'); ?>

</div><!--/#fullpage -->
