<aside class="aaeModal">
 <div class="content">
 </div>
</aside>

<header id="header" <?php if ($aResult['row1']->bild != '') : ?> style="background-image:url('<?= $row->bild; ?>');"<?php endif; ?>></header>

<div class="aaeActionBar">
 <div class="row">
 <?php if ($hat_recht): ?>
  <div class="large-3 large-offset-1 columns"><a href="<?= base_path(); ?>akteuredit/<?= $akteur_id; ?>" title="Akteur bearbeiten"><img src="<?= base_path().path_to_theme(); ?>/img/manage.svg" />Bearbeiten</a></div>
  <?php endif; ?>
  <div class="large-3 columns right" style="text-align: right;">
   <a href="https://leipziger-ecken.de/contact" title="Das Profil wurde unbefugt erstellt? Melden Sie sich hier."><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" />Melden</a>
   <a href="#share" class="popup-link" title="Akteursseite in den sozialen Netzwerken posten"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" />Teilen</a>
   <div id="share" class="popup large-3 columns">

     <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url;
 echo $base_url.'/'.current_path(); ?>" title="Auf Twitter teilen" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>

     <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="Auf Facebook teilen" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>

     <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="Auf Google+ teilen" class="g_plus button"><img alt="Google+" src="<?= base_path().path_to_theme(); ?>/img/social-googleplus-outline.svg"><span></span></a>

     <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $aResult['row1']->name; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="Teile auf Diaspora / Friendica"><img alt="" src="https://sharetodiaspora.github.io/favicon.png"></a>

   </div>
  </div>
 </div>
</div><!-- /.aaeActionBar -->

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
			   <header <?php if ($aResult['row1']->bild != '') echo 'style="background-image:url('.$aResult['row1']->bild.');"'; ?>>
					<?php if ($aResult['row1']->bild != '') echo '<img src="'.$aResult['row1']->bild.'" style="visbility:hidden;" />';
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
              <a style="font-size:0.4em;" href="<?= base_path(); ?>akteure/?tags[]=<?= $row[0]->KID; ?>" title="Zeige alle mit <?= $row[0]->kategorie; ?> getaggten Akteure">#<?= strtolower($row[0]->kategorie); ?></a>
            <?php endforeach; ?>
          <?php endif; ?></h1>

			  <h3>Beschreibung</h3>

				<?php if (!empty($aResult['row1']->beschreibung)): ?>
				<p><?= $aResult['row1']->beschreibung; ?></p>
			  <?php else : ?>
				<p><i>Hier wurde leider noch keine Beschreibung angelegt :(</i></p>
			  <?php endif; ?>

        <?php if (!empty($aResult['events'])) : ?>
        <div id="next-events">
			  <h3>Veranstaltungen</h3>
         <?php  $monat = array(
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
         ); ?>

				 <?php foreach($aResult['events'] as $event) : ?>
         <?php $eStart = explode('-', $event[0]->start); ?>
         <div class="aaeEvent row">
			   <div class="date large-2 columns button secondary"><?= $eStart[0]; ?><br /><?= $monat[$eStart[1]]; ?></div>
          <div class="content large-10 columns">
           <p><a style="line-height:1.6em;" href="<?= base_path(); ?>Eventprofil/<?= $event[0]->EID; ?>"> <strong><?= $event[0]->name; ?></strong></a>
           <span class="right"><?= $event[0]->zeit_von; ?> - <?= $event[0]->zeit_bis; ?></span></p>
           <?php if (!empty($event[0]->kurzbeschreibung)): ?>
             <div class="divider"></div>
             <?php $numwords = 30;
                   preg_match("/(\S+\s*){0,$numwords}/", $event[0]->kurzbeschreibung, $regs); ?>
             <p><?= trim($regs[0]); ?>...</p>
           <?php endif; ?>
           </div>
          </div>
		     <?php endforeach; ?>
       </div>
			  <?php endif; ?>
			 </section>

			</div>
