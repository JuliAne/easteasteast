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
  <div class="large-4 columns right" style="text-align: right;">
   <a href="<?= base_path(); ?>vcard_download/<?= $aResult['row1']->AID; ?>" title="Akteur als .vcard exportieren">Export (VCard)</a>
   <a href="https://leipziger-ecken.de/contact" title="Das Profil wurde unbefugt erstellt? Melden Sie sich hier."><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" />Melden</a>
   <a href="#share" class="popup-link" title="Akteursseite in den sozialen Netzwerken posten"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" />Teilen</a>
   <div id="share" class="popup large-3 columns">
     <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url;
 echo $base_url.'/'.current_path(); ?>" title="Auf Twitter teilen" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>
     <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="Auf Facebook teilen" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>
     <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="Auf Google+ teilen" class="g_plus button"><img alt="Google+" src="<?= base_path().path_to_theme(); ?>/img/social-googleplus-outline.svg"><span></span></a>
     <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $aResult['row1']->name; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="Teile auf Diaspora / Friendica"><img alt="Federated networks" src="<?= base_path().path_to_theme(); ?>/img/social-diaspora.png"></a>
   </div>
  </div>
 </div>
</div><!-- /.aaeActionBar -->

<div id="project" class="row">

 <aside class="left large-4 columns">

  <div class="pcard">
   <header <?php if ($aResult['row1']->bild != '') echo 'style="background-image:url('.$aResult['row1']->bild.');"'; ?>>
  	<?php if ($aResult['row1']->bild != '') echo '<img src="'.$aResult['row1']->bild.'" style="visbility:hidden;" />';
	        else echo '<img src="'.base_path().path_to_theme().'/img/project_bg.png" style="visibility:hidden;"/>';	?>
	 </header>
	</div>

			  <div id="project-info" class="pcard">
				 <?php if (!empty($aResult['row1']->oeffnungszeiten)) : ?>
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" title="Öffnunszeiten" /></span><?= $aResult['row1']->oeffnungszeiten; ?></p>
				 <div class="divider"></div>
			   <?php endif; ?>

	       <!-- TODO: Zu ergänzen mit "Bezirk" in strong-case's -->
         <?php if (!empty($aResult['adresse']->strasse) || !empty($aResult['adresse']->plz)) : ?>
			   <p><span><img src="<?= base_path().path_to_theme(); ?>/img/location_white.svg" title="Adresse" /></span><?= $aResult['adresse']->strasse; ?> <?= $aResult['adresse']->nr; ?><br />
         <?php if (!empty($aResult['adresse']->plz)) : ?><?= $aResult['adresse']->plz; ?> Leipzig</p><?php endif; ?>
				 <div class="divider"></div>
         <?php endif; ?>

         <?php if (!empty($aResult['row1']->url)) : ?>
				 <p><span><img src="<?= base_path().path_to_theme(); ?>/img/cloud_white.svg" /></span><a href="<?= $aResult['row1']->url; ?>"><?= str_replace('http://', '', $aResult['row1']->url);?></a></p>
				 <div class="divider"></div>
				<?php endif; ?>

        <?php if ($aResult['row1']->barrierefrei == '1') : ?>
        <p><span style="padding:7px 2px;"><img style="width:32px;" src="<?= base_path().path_to_theme(); ?>/img/accessibility_icon_white.svg" /></span>Barrierefreier Zugang</p>
        <div class="divider"></div>
       <?php endif; ?>

				 <?php if (!empty($aResult['adresse']->gps)) : ?>
				 <div id="map" style="width: 100%; height: 180px;"></div>
		     <?php endif; ?>

				</div>

			  <div id="project-contact" class="pcard">
			   <a href="#"><button class="button">Kontaktieren</button></a>
			  </div>

			 </aside>

			 <section id="project-content" class="large-7 large-offset-1 columns">
			  <h1><?= $aResult['row1']->name; ?><br />
         <?php if (!empty($resultTags)) : ?>
          <!--<h4>Tags:</h4>-->
          <?php foreach ($resultTags as $tag) : ?>
           <a style="font-size:0.4em;" href="<?= base_path(); ?>akteure/?tags[]=<?= $tag->KID; ?>" rel="nofollow" title="Zeige alle mit <?= $tag->kategorie; ?> getaggten Akteure">#<?= strtolower($tag->kategorie); ?></a>
          <?php endforeach; ?>
        <?php endif; ?></h1>

			  <h3>Beschreibung</h3>

				<?php if (!empty($aResult['row1']->beschreibung)): ?>
				<p><?= $aResult['row1']->beschreibung; ?></p>
			  <?php else : ?>
				<p><i>Hier wurde leider noch keine Beschreibung angelegt :(</i></p>
			  <?php endif; ?>

        <?php if (!empty($resultEvents)) : ?>
        <div id="next-events">
			   <h3>Veranstaltungen</h3>

				 <?php foreach($resultEvents as $event) : ?>
         <?php $start = new DateTime($event->start_ts);
               $ende =  new DateTime($event->ende_ts);
               $istAbgelaufen = ($start->format('Ymd') < date('Ymd')) ? true : false;
         ?>
         <div class="aaeEvent row<?= ($istAbgelaufen ? ' outdated' : ''); ?>">
			   <div class="date large-2 columns button secondary"><?= $start->format('d'); ?><br /><?= $this->monat_short[$start->format('m')]; ?></div>
          <div class="content large-10 columns">
           <p><a style="line-height:1.6em;" href="<?= base_path(); ?>Eventprofil/<?= $event->EID; ?>"> <strong><?= $event->name; ?></strong></a>
           <span class="right"><?php if($ende->format('H:i') !== '00:00') echo $start->format('H:i'); ?><?php if($ende->format('H:i') !== '00:00') echo' - '. $ende->format('H:i'); ?></span></p>
           <?php if (!empty($event->kurzbeschreibung)): ?>
             <div class="divider"></div>
             <?php $numwords = 30;
                   preg_match("/(\S+\s*){0,$numwords}/", $event->kurzbeschreibung, $regs); ?>
             <p><?= trim($regs[0]); ?><a href="<?= base_path().'eventprofil/'.$event->EID; ?>">...</a></p>
            <?php endif; ?>
           </div>
          </div>
		     <?php endforeach; ?>
       </div>
			  <?php endif; ?>
			 </section>

			</div>
