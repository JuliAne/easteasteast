<div id="eventprofil">

<?php
 $style = 'style="';
 if (!empty($resultEvent->bild)) $style .= "background-image:url('<?= $resultEvent->bild; ?>');";
 if ($map) $style .= "filter:none;padding-top:38px;";
 $style .= '"'; ?>

<header id="header" <?= $style; ?>>
 <?php if ($map) : ?><div id="map" style="height:270px;width:100%;margin-bottom:20px;"></div><?php endif; ?>
</header>

<div class="aaeActionBar">
 <div class="row" style="margin: 0 auto;">
 <?php if ($okay) : ?>
  <div class="large-3 large-offset-1 columns"><a href="<?= base_path(); ?>eventedit/<?= $resultEvent->EID; ?>" title="Event bearbeiten"><img src="<?= base_path().path_to_theme(); ?>/img/manage.svg" />Bearbeiten</a></div>
 <?php endif; ?>
  <div class="large-5 columns right" style="text-align: right;">

   <a href="<?= base_path(); ?>ics_download/<?= $resultEvent->EID; ?>" title="Event Als .ical exportieren">Export (iCal)</a>
   <a href="https://leipziger-ecken.de/contact" title="Dieses Event wurde unbefugt erstellt? Melden Sie sich hier."><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" />Melden</a>
   <a href="#share" class="popup-link" title="Event in den sozialen Netzwerken teilen"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" />Teilen</a>
   <div id="share" class="popup large-3 columns">
       <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url; echo $base_url.'/'.current_path(); ?>" title="Auf Twitter teilen" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>
       <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="Auf Facebook teilen" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>
       <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="Auf Google+ teilen" class="g_plus button"><img alt="Google+" src="<?= base_path().path_to_theme(); ?>/img/social-googleplus-outline.svg"><span></span></a>
       <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $resultEvent->name; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="Teile auf Diaspora / Friendica"><img alt="Federated networks" src="<?= base_path().path_to_theme(); ?>/img/social-diaspora.png"></a>
    </div>
   </div>
  </div>
 </div><!-- /.aaeActionBar -->

 <div id="project" class="row" itemscope itemtype="http://schema.org/SocialEvent">

  <div id="event-data" class="large-4 columns">
    <div class="pcard">
     <header <?php if (!empty($resultEvent->bild)) echo 'style="background-image:url('.$resultEvent->bild.');"'; ?>>
      <?php if (!empty($resultEvent->bild)) echo '<img src="'.$resultEvent->bild.'" style="visbility:hidden;" />';
            else echo '<img src="'.base_path().path_to_theme().'/img/event_bg.png" style="visibility:hidden;" itemprop="image"/>';	?>
     </header>
    </div>

   <div id="project-info" class="pcard" style="margin-top:5px;">
     <p><span><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span>
     <strong style="color:grey;">Start: </strong><a href="<?= base_path(); ?>events/?day=<?= $resultEvent->start->format('Y-m-d'); ?>" rel="nofollow" itemprop="startDate" content="<?= $resultEvent->start->format('Y-m-d'); ?>"><?= $resultEvent->start->format('d.m.Y'); ?></a>
     <?= ($resultEvent->start->format('s') == '01' ? $resultEvent->start->format('H:i').' Uhr' : ''); ?>
     <?php if ($resultEvent->ende->format('Ymd') !== '10000101' || $resultEvent->ende->format('s') == '01') : ?>
       <br /><strong style="color:grey">Bis: </strong>
       <?= ($resultEvent->ende->format('Ymd') !== '10000101' ? '<a href="'.base_path().'events/?day='.$resultEvent->ende->format('Y-m-d').'" rel="nofollow" itemprop="endDate" content="'.$resultEvent->ende->format('Y-m-d').'">'.$resultEvent->ende->format('d.m.Y').'</a>' : ''); ?>
       <?= ($resultEvent->ende->format('s') == '01' ? ' '.$resultEvent->ende->format('H:i').' Uhr' : ''); ?>
    <?php endif; ?>
    </p>
   </div>

</div>

<div id="project-content" class="large-7 large-offset-1 columns">
 <h1><span itemprop="name"><?= $resultEvent->name; ?></span><br />
 <?php if (!empty($sparten)) : ?>
   <?php foreach ($sparten as $row) : ?>
     <a style="font-size:0.4em;" href="<?= base_path(); ?>events/?filterTags[]=<?= $row->KID; ?>" rel="nofollow" title="Zeige alle mit '<?= $row->kategorie; ?>' getaggten Events">#<?= strtolower($row->kategorie); ?></a>
   <?php endforeach; ?>
 <?php endif; ?></h1>

 <?php if(!empty($resultEvent->kurzbeschreibung)) : ?>
  <h4 style="padding: 10px 0;">Beschreibung</h4>
  <p itemprop="description"><?= $resultEvent->kurzbeschreibung; ?></p>
 <?php endif; ?>

 <h4 style="padding: 10px 0;">Veranstalter</h4>
  <section itemscope itemprop="location"  itemtype="http://schema.org/Place">

  <?php if (!empty($resultAdresse->gps)) : $gps = explode(',', $resultAdresse->gps); ?>
   <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
    <meta itemprop="latitude" content="<?= $gps[0]; ?>" />
    <meta itemprop="longitude" content="<?= $gps[1]; ?>" />
   </div>
    <?php endif; ?>

  <?php foreach ($ersteller as $row2) : ?>
   <p><strong>Erstellt von:</strong> <?= $row2->name; ?> am <?= $resultEvent->created->format('d.m.Y'); ?></p>
  <?php endforeach; ?>

   <?php if (empty($resultAkteur)) : ?>
   <p><strong>Privater Veranstalter</strong></p>
   <?php else : ?>
   <p><strong>Akteur:</strong> <a href="<?= base_path(); ?>Akteurprofil/<?= $resultAkteur['AID']; ?>" title="Profil von <?= $resultAkteur['name']; ?> besuchen" itemprop="name"><?= $resultAkteur['name']; ?></a></p>
   <?php endif; ?>

   <?php if (!empty($resultAdresse)) : ?>
    <div id="adress" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
    <p><strong>Ort:</strong>

    <?php if (!empty($resultAdresse->strasse) && !empty($resultAdresse->nr)) : ?>
     <span itemprop="streetAddress"><?= $resultAdresse->strasse.' '.$resultAdresse->nr; ?></span>
    <?php endif; ?>

   <?php if (!empty($resultAdresse->plz)) : ?>
      - <span itemprop="postalCode"><?= $resultAdresse->plz; ?></span> <span itemprop="addressLocality">Leipzig</span>
   <?php endif; ?>

   <?php if (!empty($resultBezirk->bezirksname)) : ?>
    <?= $resultBezirk->bezirksname; ?>
   <?php endif; ?>

  </p><?php endif; ?>

  <?php if (!empty($resultEvent->url)) : ?>
    <br /><p><strong>Weitere Informationen: </strong><a href="<?= $resultEvent->url; ?>" itemprop="url"><?= $resultEvent->url; ?></a></p>
  <?php else : ?>
    <meta itemprop="url" content="<?= base_path().'eventprofil/'.$resultEvent->EID; ?>" Z>
  <?php endif; ?>

   </div></section>
  </div>
 </div><!-- /#project -->
</div><!-- /#eventprofil -->
