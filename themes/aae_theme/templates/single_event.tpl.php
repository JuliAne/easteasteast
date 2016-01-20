<?php if (!empty($_SESSION['sysmsg'])) : ?>
<div id="alert">
  <?php foreach ($_SESSION['sysmsg'] as $msg): ?>
    <?= $msg; ?>
  <?php endforeach; ?>
  <a href="#" class="close">x</a>
</div>
<?php unset($_SESSION['sysmsg']); endif; ?>

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
 <div class="row">
 <?php if ($okay) : ?>
  <div class="large-3 large-offset-1 columns"><a href="<?= base_path(); ?>eventedit/<?= $resultEvent->EID; ?>" title="Event bearbeiten"><img src="<?= base_path().path_to_theme(); ?>/img/manage.svg" />Bearbeiten</a></div>
 <?php endif; ?>
  <div class="large-5 columns right" style="text-align: right;">

   <a href="<?= base_path(); ?>ics_download/<?= $resultEvent->EID; ?>" title="Event Als .ical exportieren">Export (iCal)</a>
   <a href="https://leipziger-ecken.de/contact" title="Dieses Event wurde unbefugt erstellt? Melden Sie sich hier."><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" />Melden</a>
   <a href="#share" class="popup-link" title="Event in den sozialen Netzwerken teilen"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" />Teilen</a>
   <div id="share" class="popup large-3 columns">

       <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url;
   echo $base_url.'/'.current_path(); ?>" title="Auf Twitter teilen" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>

       <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="Auf Facebook teilen" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>

       <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="Auf Google+ teilen" class="g_plus button"><img alt="Google+" src="<?= base_path().path_to_theme(); ?>/img/social-googleplus-outline.svg"><span></span></a>

       <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $resultEvent->name; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="Teile auf Diaspora / Friendica"><img alt="" src="https://sharetodiaspora.github.io/favicon.png"></a>
    </div>
   </div>
  </div>
 </div><!-- /.aaeActionBar -->

 <div id="project" class="row">

 <?php $start = explode("-", $resultEvent->start); ?>

  <div id="event-data" class="large-4 columns">
    <div class="pcard">
     <header <?php if (!empty($resultEvent->bild)) echo 'style="background-image:url('.$resultEvent->bild.');"'; ?>>
      <?php if (!empty($resultEvent->bild)) echo '<img src="'.$resultEvent->bild.'" style="visbility:hidden;" />';
            else echo '<img src="'.base_path().path_to_theme().'/img/event_bg.png" style="visibility:hidden;"/>';	?>
     </header>
    </div>

   <div id="project-info" class="pcard" style="margin-top:5px;">
     <p><span><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span>
     <a href="<?= base_path(); ?>events/?day=<?= $resultEvent->start; ?>"><?= $start[0].'.'.$start[1].'.'.$start[2]; ?></a>
     <?php if (!empty($resultEvent->ende)) echo ' - <a href="'.base_path().'events/?day='.$resultEvent->ende.'">'.$resultEvent->ende.'</a>'; ?>
     <br /><?= $resultEvent->zeit_von; ?><?php  if (!empty($resultEvent->zeit_bis)) echo ' - '.$resultEvent->zeit_bis.' Uhr'; ?>
    </p>
   </div>

<div class="divider" style="padding: 20px 0;"></div>

</div>

<div id="project-content" class="large-7 large-offset-1 columns">
 <h1><?= $resultEvent->name; ?>
 <?php if (!empty($sparten)) : ?>
   <?php foreach ($sparten as $row) : ?>
     <a style="font-size:0.4em;" href="<?= base_path(); ?>events/?tags[]=<?= $row->KID; ?>" title="Zeige alle mit <?= $row->kategorie; ?> getaggten Events">#<?= strtolower($row->kategorie); ?></a>
   <?php endforeach; ?>
 <?php endif; ?></h1>

 <?php if(!empty($resultEvent->kurzbeschreibung)) : ?>
  <h4 style="padding: 10px 0;">Beschreibung</h4>
  <p><?= $resultEvent->kurzbeschreibung; ?></p>
 <?php endif; ?>

 <h4 style="padding: 10px 0;">Veranstalter</h4>

 <?php foreach ($ersteller as $row2) : ?>
   <p><strong>Erstellt von:</strong> <?= $row2->name; ?></p>
 <?php endforeach; ?>

   <?php if(empty($resultAkteur)) : ?>
   <p><strong>Privater Veranstaltater</strong></p>
   <?php else : ?>
   <p><strong>Akteur:</strong> <a href="<?= base_path(); ?>Akteurprofil/<?= $resultAkteur['AID']; ?>" title="Profil von <?= $resultAkteur['name']; ?> besuchen"><?= $resultAkteur['name']; ?></a></p>
   <?php endif; ?>

   <?php if(!empty($resultAdresse)) : ?>
    <p><strong>Ort:</strong>

    <?php if($resultAdresse->strasse != "" && $resultAdresse->nr != "") : ?>
       <?= $resultAdresse->strasse.' '.$resultAdresse->nr; ?>
    <?php endif; ?>

   <?php if($resultAdresse->plz != "") : ?>
      - <?= $resultAdresse->plz; ?> Leipzig
   <?php endif; ?>

   <?php  if($resultBezirk->bezirksname != "") : ?>
      <?= $resultBezirk->bezirksname; ?>
   <?php endif; ?>

   <?php //if($resultAdresse->gps != "") : ?>

  </p><?php endif; ?>

  <?php if($resultEvent->url != "") : ?>
    <br /><p><strong>Weitere Informationen: </strong><a href="<?= $resultEvent->url; ?>"><?= $resultEvent->url; ?></a></p>
  <?php endif; ?>

  </div>
 </div><!-- /#project -->
</div><!-- /#eventprofil -->
