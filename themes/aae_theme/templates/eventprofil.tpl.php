<style type="text/css">
.region-content, #contentRow {
 width: 100% !important;
 max-width: 100% !important;
 margin: 0;
 overflow: hidden;
}
</style>
<?php if ($this->isOwner) : ?>  
<script type="text/javascript">
 function ajaxRemoveAppointment(elem,eid) {
  $.ajax({
    url: "../ajax/removeEventChildren/" + eid,
   })
  .done(function(data) {
   if (data) {
    $(elem).fadeOut('slow');
   }
  });
 }
</script>
<?php endif; ?>
<div id="eventprofil"<?= ($showMap) ? ' class="hasMap"' : ''; ?>>

<?php
 $style = 'style="';
 if (!empty($resultEvent->bild)) $style .= "background-image:url('".$resultEvent->bild."');";
 if ($showMap) $style .= "filter:none !important;-webkit-filter:none !important;padding-top:38px;";
 $style .= '"'; ?>

<header id="header" <?= $style; ?>>
 <?php if ($showMap) : ?><div id="map" style="height:280px;width:100%;margin-bottom:20px;"></div><?php endif; ?>
</header>

<div class="aaeActionBar">
 <div class="row" style="margin: 0 auto;">
 <?php if ($this->isOwner) : ?>
  <div class="large-3 large-offset-1 columns"><a href="<?= base_path(); ?>eventprofil/<?= $resultEvent->EID; ?>/edit" title="<?= t('Event bearbeiten'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/manage.svg" /><?= t('Bearbeiten'); ?></a></div>
 <?php endif; ?>
  <div class="large-5 columns right" style="text-align: right;">
   <a href="<?= base_path(); ?>eventprofil/<?= $resultEvent->EID; ?>/ics_download/" title="<?= t('Event Als .ical exportieren'); ?>">Export (iCal)</a>
   <a href="https://leipziger-ecken.de/contact" title="<?= t('Dieses Event wurde unbefugt erstellt? Melden Sie sich hier.'); ?>" class="hide-for-small-only"><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" /><?= t('Melden'); ?></a>
   <a href="#share" class="popup-link" title="<?= t('Event in den sozialen Netzwerken teilen'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" /><?= t('Teilen'); ?></a>
   <div id="share" class="popup large-3 columns">
    <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url; echo $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen',array('!network'=>'Twitter')); ?>" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>
    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen',array('!network'=>'Facebook')); ?>" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>
    <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen',array('!network'=>'Google+')); ?>" class="g_plus button"><img alt="Google+" src="<?= base_path().path_to_theme(); ?>/img/social-googleplus-outline.svg"><span></span></a>
    <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $resultEvent->name; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="<?= t('Auf !network teilen',array('!network'=>'Diaspora/Friendica')); ?>"><img alt="Federated networks" src="<?= base_path().path_to_theme(); ?>/img/social-diaspora.png"></a>
    </div>
   </div>
  </div>
 </div><!-- /.aaeActionBar -->

 <div id="project" class="row" itemscope itemtype="http://schema.org/Event">
  <?php global $base_root; ?>
  <meta itemprop="url" content="<?= $base_root .'/eventprofil/'.$resultEvent->EID; ?>" />
  <meta itemprop="name" content="<?= $resultEvent->name; ?>" />

  <div id="event-data" class="large-4 columns">
    <div class="pcard">
     <header <?php if (!empty($resultEvent->bild)) echo 'style="background-image:url('.$resultEvent->bild.');"'; ?>>
      <?php if (!empty($resultEvent->bild)) echo '<img src="'.$resultEvent->bild.'" style="visbility:hidden;" itemprop="image" alt="'. t('Logo vom Event').'" />';
            else echo '<img src="'.base_path().path_to_theme().'/img/event_bg.png" style="visibility:hidden;" />';	?>
     </header>
    </div>

   <div id="project-info" class="pcard" style="margin-top:5px;">
     <p><span class="icon"><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span>
     <strong style="color:rgb(96,94,94);">Start: </strong><a href="<?= base_path(); ?>events/?day=<?= $resultEvent->start->format('Y-m-d'); ?>" rel="nofollow" itemprop="startDate" content="<?= $resultEvent->start->format('Y-m-dTH:i'); ?>"><?= $resultEvent->start->format('d.m.Y'); ?></a>
     <?= ($resultEvent->start->format('s') == '01' ? ', '.$resultEvent->start->format('H:i').' Uhr' : ''); ?>
     <?php if ($resultEvent->ende->format('Ymd') !== '10000101' || $resultEvent->ende->format('s') == '01') : ?>
       <br /><strong style="color:rgb(96,94,94);">Bis: </strong>
       <?= ($resultEvent->ende->format('Ymd') !== '10000101' ? '<a href="'.base_path().'events/?day='.$resultEvent->ende->format('Y-m-d').'" rel="nofollow" itemprop="endDate" content="'.$resultEvent->ende->format('Y-m-dTH:i').'">'.$resultEvent->ende->format('d.m.Y').'</a>' : ''); ?>
       <?= ($resultEvent->ende->format('s') == '01' ? ' '.$resultEvent->ende->format('H:i').' Uhr' : ''); ?></p>
    <?php endif; ?>
    <?php if (!empty($resultEvent->childrenEvents)) : ?>
     <div class="divider"></div>
     <ul style="padding-left:100px;font-size:0.9em;padding-bottom:15px;">
      <li style="line-height:1.7em;"><strong><?= t('Weitere Termine:'); ?></strong></li>
      <?php foreach ($resultEvent->childrenEvents as $event) : ?>
       <?php $adminFeature = ($this->isOwner ? ' class="removeAppointment" title="'.t('Termin entfernen').'" onclick="javascript:ajaxRemoveAppointment(this,'.$event->EID.');return false;"' : ''); ?>
       <li<?= $adminFeature; ?> style="line-height:1.4em;"><a href="<?= base_path().'events/?day='.$event->start->format('Y-m-d'); ?>" rel="nofollow">
       <?= $event->start->format('d.m.Y'); ?></a></li>
      <?php endforeach; ?>
      </ul>
     <?php endif; ?>
   </div>

</div>

<div id="project-content" class="large-7 large-offset-1 columns">
  <ol id="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
   <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
     <a itemprop="item" href="<?= $base_url; ?>">
     <span itemprop="name" title="<?= t('Startseite'); ?>"><?= t('Startseite'); ?></span></a>
     <meta itemprop="position" content="0" />
   </li>  
   <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
     <a itemprop="item" href="<?= $base_url; ?>/events">
     <span itemprop="name" title="<?= t('Events'); ?>"><?= t('Events'); ?></span></a>
     <meta itemprop="position" content="1" />
   </li>
   <li id="activeEvent" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
     <a itemprop="item" href="<?= $base_url; ?>/eventprofil/<?= $resultEvent->EID; ?>">
     <span itemprop="name" title="<?= $resultEvent->name; ?>"><?= $resultEvent->name; ?></span></a>
     <meta itemprop="position" content="2" />
   </li>
  </ol>

 <?php if (!empty($resultEvent->tags)) : ?>
  <aside id="eventSparten">
  <?php foreach ($resultEvent->tags as $row) : ?>
   <a href="<?= base_path(); ?>events/?filterTags[]=<?= $row->KID; ?>" rel="nofollow" title="<?= t('Zeige alle mit !kategorie getaggten Events',array('!kategorie'=>$row->kategorie)); ?>">#<?= strtolower($row->kategorie); ?></a>
  <?php endforeach; ?>
  </aside>
 <?php endif; ?>

 <?php if(!empty($resultEvent->kurzbeschreibung)) : ?>
  <div class="event-content">
   <h4 style="padding: 10px 0;"><?= t('Beschreibung'); ?></h4>
   <p itemprop="description"><?= $resultEvent->kurzbeschreibung; ?></p>
  </div>
 <?php endif; ?>

 <h4 style="padding: 10px 0;"><?= t('Veranstalter'); ?></h4>
  <section itemscope itemprop="location"  itemtype="http://schema.org/Place">

  <?php if (!empty($resultEvent->adresse->gps_lat)) : ?>
   <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
    <meta itemprop="latitude" content="<?= $resultEvent->adresse->gps_lat; ?>" />
    <meta itemprop="longitude" content="<?= $resultEvent->adresse->gps_long; ?>" />
   </div>
  <?php endif; ?>
 
   <p><strong><?= t('Erstellt von'); ?>:</strong> <?= $resultEvent->ersteller; ?> <?= t('am'); ?> <?= $resultEvent->created->format('d.m.Y'); ?></p>
   <?php if (empty($resultEvent->akteur)) : ?>
   <p><strong><?= t('Privater Veranstalter'); ?></strong></p>
   <?php elseif (isset($resultEvent->festival)) : ?>
   <p><strong><?= t('Teil des'); ?> <a href="<?= base_path().$resultEvent->festival->alias; ?>" title="<?= t('Zur Festivalseite'); ?>"><?= $resultEvent->festival->name; ?></a></strong></p>
   <?php else : ?>
   <p><strong><?= t('Akteur'); ?>:</strong> <a href="<?= base_path(); ?>akteurprofil/<?= $resultEvent->akteur->AID; ?>" title="<?= t('Akteurprofil von !username besuchen',array('!username'=> $resultEvent->akteur->name)); ?>" itemprop="name" content="<?= $resultEvent->akteur->name; ?>"><?= $resultEvent->akteur->name; ?></a></p>
   <?php endif; ?>

   <?php if (!empty($resultEvent->adresse)) : ?>
    <div id="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
    <p><strong><?= t('Ort'); ?>:</strong>

    <?php if (!empty($resultEvent->adresse->strasse) && !empty($resultEvent->adresse->nr)) : ?>
     <span itemprop="streetAddress"><?= $resultEvent->adresse->strasse.' '.$resultEvent->adresse->nr; ?></span>
    <?php endif; ?>

   <?php if (!empty($resultEvent->adresse->plz)) : ?>
      - <span itemprop="postalCode"><?= $resultEvent->adresse->plz; ?></span> <span itemprop="addressLocality">Leipzig</span>
   <?php endif; ?>

   <?php if (!empty($resultEvent->adresse->bezirksname)) : ?>
    <?= $resultEvent->adresse->bezirksname; ?>
   <?php endif; ?>

  </p><?php endif; ?>

  <?php if (!empty($resultEvent->url)) : ?>
   <br /><p><strong><?= t('Weitere Informationen'); ?>: </strong><a href="<?= $resultEvent->url; ?>" itemprop="sameAs" target="_blank"><?= $resultEvent->url; ?></a></p>
  <?php endif; ?>

   </div></section>
  </div>
 </div><!-- /#project -->
</div><!-- /#eventprofil -->
