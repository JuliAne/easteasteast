<?php // Wird eingelesen und ersetzt in aae_data/akteurprofil.php ?>


		<header id="header" style="background:url(<?= base_path().path_to_theme(); ?>/pcard_bg.jpg);"></header>

			<div id="project" class="row">

			 <aside class="left large-4 columns">

			  <div class="pcard">
			   <header></header>
				 <?= $aResult->$kurzbeschreibung; ?>
			  </div>

			  <div id="project-info" class="pcard">
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span>12:00 - 18:30</p>
			   <div class="divider"></div>
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/location_white.svg" /></span>Dresdner Strasse 18<br /><strong>Reudnitz</strong></p>
			   <img src="<?= base_path().path_to_theme(); ?>/img/map_test.png" style="width: 100%;" />
			  </div>

			  <div id="project-contact" class="pcard">
			   <button class="button">Kontaktieren</button>
			  </div>
			 </aside>

			 <section id="project-content" class="large-7 large-offset-1 columns">
			  <h1>Fiskalambitionierte Bouletten</h1>

			  <h3>Beschreibung</h3>

			  <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.<p>
			  <p>At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet...</p>

			  <h3>N&auml;chste Veranstaltungen</h3>

			  <ul id="next-events">
			   <li><span><a href="#">Cosplay N&auml;h-Workshop</a></span><br />Donnerstag, 22.07.2015: 12:00 Uhr bis 15:30 Uhr</li>
			   <li><span><a href="#">Lorem ipsum sed</a></span><br />Freitag, 18.08.2015: 18:00 bis 23:00</li>
			</ul>

			 </section>

			</div>
