<aside class="aaeModal">
 <div class="content">
 </div>
</aside>

<header id="header"
<?php if ($aResult['row1']->bild != '') : ?> style="background:url(<?= $row->bild; ?>);" <?php endif; ?>></header>

<div id="akteurActions">
 <div class="row">
 <?php if ($hat_recht): ?>
  <div class="large-3 large-offset-1 columns"><a href="<?= base_path(); ?>akteuredit/<?= $akteur_id; ?>" title="Akteur bearbeiten"><img src="<?= base_path().path_to_theme(); ?>/img/manage.svg" />Bearbeiten</a></div>
  <?php endif; ?>
  <div class="large-3 columns right" style="text-align: right;">
   <a href="https://leipziger-ecken.de/contact" title="Das Profil wurde unbefugt erstellt? Melden Sie sich hier."><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" />Melden</a>
   <a href="#share" class="popup-link" title="Akteursseite in den sozialen Netzwerken posten"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" />Teilen</a>
   <div id="share" class="popup large-3 columns">

     <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url;
 echo $base_url.'/'.current_path(); ?>" title="Auf Twitter teilen" class="twitter button"><img alt="icons/twitter.png" src="http://fadeco.info/system/cms/themes/defaults/img/icons/twitter.png"><span></span></a>

     <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="Auf Facebook teilen" class="fb button"><img alt="icons/fb.png" src="http://fadeco.info/system/cms/themes/defaults/img/icons/fb.png"><span></span></a>

     <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="Auf Google+ teilen" class="g_plus button"><img alt="icons/g_plus.png" src="http://fadeco.info/system/cms/themes/defaults/img/icons/g_plus.png"><span></span></a>

     <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $aResult['row1']->name; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="Teile auf Diaspora / Friendica"><img alt="" src="https://sharetodiaspora.github.io/favicon.png"></a>

   </div>
  </div>
 </div>
</div>

<?php if (!empty($_SESSION['sysmsg'])) : ?>
<div id="alert">
  <?php foreach ($_SESSION['sysmsg'] as $msg): ?>
    <?= $msg; ?>
  <?php endforeach; ?>
  <a href="#" class="close">x</a>
</div>
<?php unset($_SESSION['sysmsg']); endif; ?>

			<div id="project" class="row">

			 <aside class="left large-4 columns">

			  <div class="pcard">
			   <header <?php if ($aResult['row1']->bild != '') echo 'style="background:url('.$row->bild.');"'; ?>>
					<?php if ($aResult['row1']->bild != '') echo '<img src="'.$row->bild.'" style="visbility:hidden;" />';
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
			   <a href="#"><button class="button">Kontaktieren</button></a>
			  </div>

			 </aside>

			 <section id="project-content" class="large-7 large-offset-1 columns">
			  <h1><?= $aResult['row1']->name; ?><br />
          <?php if (!empty($resulttags)) : ?>
            <!--<h4>Tags:</h4>-->
            <?php foreach ($resulttags as $row) : ?>
              <a style="font-size:0.4em;" href="#" title="TODO: Weiterleitung auf /akteure/?$tags=row->KID">#<?= strtolower($row[0]->kategorie); ?></a>
            <?php endforeach; ?>
          <?php endif; ?></h1>

			  <h3>Beschreibung</h3>

				<?php if ($aResult['row1']->beschreibung !== ''): ?>
				<p><?= $aResult['row1']->beschreibung; ?></p>
			  <?php else : ?>
				<p><i>Hier wurde leider noch keine Beschreibung angelegt :(</i></p>
			  <?php endif; ?>

        <?php if ($aResult['events'] != '') : ?>
			  <h3>N&auml;chste Veranstaltungen</h3>

			  <ul id="next-events">
				 <?php foreach($aResult['events'] as $event) : ?>
			   <li><span><a href="<?= base_path(); ?>Eventprofil/<?= $event[0]->EID; ?>"><?= $event[0]->name; ?></a></span><br />
         <p><?= $event[0]->kurzbeschreibung; ?></p>
         <?= $event[0]->start; ?> - <?= $event[0]->ende; ?></li>
		     <?php endforeach; ?>
		  	</ul>
			  <?php endif; ?>
			 </section>

			</div>
