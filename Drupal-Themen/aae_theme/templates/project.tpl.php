<?php // Wird eingelesen und ersetzt in aae_data/akteurprofil.php ?>


		<header id="header" <?php if ($aResult['row1']->bild != '') echo 'style="background:url(sites/all/modules/aae_data/'.$row->bild.');"'; ?>></header>

			<div id="project" class="row">

			 <aside class="left large-4 columns">

			  <div class="pcard">
			   <header></header>
			  </div>

			  <div id="project-info" class="pcard">
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span><?= $aResult['row1']->oeffnungszeiten; ?></p>
			   <div class="divider"></div>
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/location_white.svg" /></span><?= $aResult['row2']->strasse; ?> <?= $aResult['row2']->nr; ?><br /><strong>Reudnitz</strong></p>
			   <img src="<?= base_path().path_to_theme(); ?>/img/map_test.png" style="width: 100%;" />
			  </div>

			  <div id="project-contact" class="pcard">
			   <a href="<?= base_path().'akteurkontakt/'.$akteur_id; ?>"><button class="button">Kontaktieren</button></a>
				 <?php if ($aResult['hat_recht']): ?><a href="<?= base_path().'akteuredit/'.$akteur_id; ?>"><button class="button secondary">Projekt bearbeiten</button></a><?php endif; ?>
			  </div>
			 </aside>

			 <section id="project-content" class="large-7 large-offset-1 columns">
			  <h1><?= $aResult['row1']->name; ?></h1>

				<?= print_r($aResult['row1']); ?>

			  <h3>Beschreibung</h3>

        <?= $aResult['row1']->kurzbeschreibung; ?>

			  <!--<h3>N&auml;chste Veranstaltungen</h3>

			  <ul id="next-events">
			   <li><span><a href="#">Cosplay N&auml;h-Workshop</a></span><br />Donnerstag, 22.07.2015: 12:00 Uhr bis 15:30 Uhr</li>
			   <li><span><a href="#">Lorem ipsum sed</a></span><br />Freitag, 18.08.2015: 18:00 bis 23:00</li>
			</ul> -->

  <?= print_r($aResult['row2']); ?>
			 </section>

			</div>
