<header id="header"
<?php if ($aResult['row1']->bild != '') : ?> style="background:url(<?= base_path().$modulePath.'/'.$row->bild; ?>);" <?php endif; ?>></header>

			<div id="project" class="row">

			 <aside class="left large-4 columns">

			  <div class="pcard">
			   <header <?php if ($aResult['row1']->bild != '') echo 'style="background:url('.base_path().$modulePath.'/'.$row->bild.');"'; ?>>
					<?php if ($aResult['row1']->bild != '') echo '<img src="'.base_path().$modulePath.'/'.$row->bild.'" style="visbility:hidden;" />';
					      else echo '<img src="'.base_path().path_to_theme().'/img/project_bg.png" style="visibility:hidden;"/>';	?>
				 </header>
			  </div>

			  <div id="project-info" class="pcard">
				 <?php if ($aResult['row1']->oeffnungszeiten != '') : ?>
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span><?= $aResult['row1']->oeffnungszeiten; ?></p>
				 <div class="divider"></div>
			   <?php endif; ?>

	       <!-- TODO: Zu ergänzen mit "Bezirk" in strong-case's -->
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/location_white.svg" /></span><?= $aResult['row2']->strasse; ?> <?= $aResult['row2']->nr; ?><br /><?= $aResult['row2']->plz; ?> Leipzig</p>
				 <div class="divider"></div>

         <?php if ($aResult['row1']->url != '') : ?>
				 <p><span><img src="<?= base_path().path_to_theme(); ?>/img/cloud_white.svg" /></span><a href="<?= $aResult['row1']->url; ?>"><?= str_replace('http://', '', $aResult['row1']->url);?></a></p>
				 <div class="divider"></div>
				<?php endif; ?>

				 <?php if($aResult['row2']->gps != '') : ?>
				 <div id="map" style="width: 100%; height: 180px;"></div>
		     <?php endif; ?>

				</div>

			  <div id="project-contact" class="pcard">
			   <a href="<?= base_path().'akteurkontakt/'.$akteur_id; ?>"><button class="button">Kontaktieren</button></a>

				 <?php if ($aResult['hat_recht']): // FUNKTIONIERT NICHT... ?>
				 <a href="<?= base_path().'akteuredit/'.$akteur_id; ?>"><button class="button secondary">Projekt bearbeiten</button></a>
				 <?php endif; ?>

			  </div>
			 </aside>

			 <section id="project-content" class="large-7 large-offset-1 columns">
			  <h1><?= $aResult['row1']->name; ?></h1>

        <?php if ($aResult['row1']->kurzbeschreibung != '') : ?>
			  <h3>Beschreibung</h3>
        <p><?= $aResult['row1']->kurzbeschreibung; ?><p>
				<?php else : ?>
				<p><i>Hier wurde leider noch keine Akteursbeschreibung angelegt :(</i></p>
			  <?php endif; ?>

				<!--<a href="#" onclick="javascript:alert($('#printr').html());">Zeige Print_r</a>

				<div id="printr" style="display:none;">
				<?= print_r($aResult['row1']); ?>
				<?= print_r($aResult['row2']); ?>
			</div> -->

        <?php if ($aResult['events'] != '') : ?>
			  <br /><h3>N&auml;chste Veranstaltungen</h3>

			  <ul id="next-events">
				 <?php foreach($aResult['events'] as $event) : ?>
			   <li><span><a href="<?= base_path(); ?>?q=Eventprofil/<?= $event->EID; ?>"><?= $event->name; ?> </a></span><br /><?= $event->start; ?> bis <?= $event->ende; ?></li>
		     <?php endforeach; ?>
		  	</ul>
			  <?php endif; ?>

			 </section>

			</div>
