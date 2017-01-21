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
<div id="eventprofil"<?= ($this->showMap) ? ' class="hasMap"' : ''; ?>>

<?php
 $style = 'style="';
 if (!empty($this->bild)) $style .= "background-image:url('".$this->bild."');";
 if ($this->showMap) $style .= "filter:none !important;-webkit-filter:none !important;padding-top:38px;";
 $style .= '"';
 $this->seoName = str_replace(array('"',"'"),'',$this->name); ?>

<header id="header" <?= $style; ?>>
 <?php if ($this->showMap) : ?><div id="map" style="height:280px;width:100%;margin-bottom:20px;"></div><?php endif; ?>
</header>

<div class="aaeActionBar">
 <div class="row" style="margin: 0 auto;">
 <?php if ($this->isOwner) : ?>
  <div class="large-3 large-offset-1 columns"><a href="<?= base_path(); ?>eventprofil/<?= $this->event_id; ?>/edit" title="<?= t('Event bearbeiten'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/manage.svg" /><?= t('Bearbeiten'); ?></a></div>
 <?php endif; ?>
  <div class="large-5 columns right" style="text-align: right;">
   <a href="<?= base_path(); ?>eventprofil/<?= $this->event_id; ?>/ics_download/" title="<?= t('Event Als .ical exportieren'); ?>">Export (iCal)</a>
   <a href="https://leipziger-ecken.de/contact" title="<?= t('Dieses Event wurde unbefugt erstellt? Melden Sie sich hier.'); ?>" class="hide-for-small-only"><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" /><?= t('Melden'); ?></a>
   <a href="#share" class="popup-link" title="<?= t('Event in den sozialen Netzwerken teilen'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" /><?= t('Teilen'); ?></a>
   <div id="share" class="popup large-3 columns">
    <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url; echo $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen',array('!network'=>'Twitter')); ?>" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>
    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen',array('!network'=>'Facebook')); ?>" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>
    <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen',array('!network'=>'Google+')); ?>" class="g_plus button"><img alt="Google+" src="<?= base_path().path_to_theme(); ?>/img/social-googleplus-outline.svg"><span></span></a>
    <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $this->seoName . ' '. t('auf leipziger-ecken.de'); ?>&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="<?= t('Auf !network teilen',array('!network'=>'Diaspora/Friendica')); ?>"><img alt="Federated networks" src="<?= base_path().path_to_theme(); ?>/img/social-diaspora.svg"></a>
    </div>
   </div>
  </div>
 </div><!-- /.aaeActionBar -->

 <div id="project" class="row" itemscope itemtype="http://schema.org/Event">
  <?php global $base_root; ?>
  <meta itemprop="url" content="<?= $base_root .'/eventprofil/'.$this->event_id; ?>" />
  <meta itemprop="name" content="<?= $this->seoName; ?>" />

  <div id="event-data" class="large-4 columns">
   <h3 id="mobileTitle" title="<?= $this->name; ?>" class="hide-for-medium"><?= $this->name; ?></h3>
    <div class="pcard">
     <header <?php if (!empty($this->bild)) echo 'style="background-image:url('.$this->bild.');"'; ?>>
      <?php if (!empty($this->bild)) echo '<img src="'.$this->bild.'" style="visbility:hidden;" itemprop="image" alt="'. t('Logo vom Event').'" />';
            else echo '<img src="'.base_path().path_to_theme().'/img/event_bg.png" style="visibility:hidden;" />';	?>
     </header>
    </div>

   <div id="project-info" class="pcard" style="margin-top:5px;">
     <p title="<?= t('Datum und Uhrzeit'); ?>"><span class="icon"><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span>
     <strong style="color:rgb(60,60,60);"><?= t('Start:'); ?> </strong><a href="<?= base_path(); ?>events/?day=<?= $this->start->format('Y-m-d'); ?>" rel="nofollow" itemprop="startDate" content="<?= $this->start->format('Y-m-dTH:i'); ?>"><?= $this->dayNames[date('N', $this->start->getTimestamp())-1]; ?>, <?= $this->start->format('d.m.Y'); ?></a>
     <?= ($this->has_starting_time ? ', '.$this->starting_time .' '. t('Uhr') : ''); ?>
     <?php if ($this->ende->format('Ymd') !== '10000101' || $this->has_ending_time) : ?>
      <br /><strong style="color:rgb(60,60,60);"><?= t('Bis:'); ?> </strong>
      <?= ($this->ende->format('Ymd') !== '10000101' && $this->ende->format('Ymd') != $this->start->format('Ymd') ? '<a href="'.base_path().'events/?day='.$this->ende->format('Y-m-d').'" rel="nofollow" itemprop="endDate" content="'.$this->ende->format('Y-m-dTH:i').'">'. $this->dayNames[date('N', $this->ende->getTimestamp())-1]. ', '. $this->ende->format('d.m.Y').'</a>' : ''); ?>
      <?= ($this->has_ending_time ? ' '. $this->ending_time .' '. t('Uhr') : ''); ?></p>
     <?php endif; ?>
    <div class="divider"></div>

    <?php if (!empty($this->adresse)) : ?>

    <div itemscope itemprop="location" itemtype="http://schema.org/Place">

    <?php if (!empty($this->adresse->gps_lat)) : ?>
    <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
     <meta itemprop="latitude" content="<?= $this->adresse->gps_lat; ?>" />
     <meta itemprop="longitude" content="<?= $this->adresse->gps_long; ?>" />
    </div>
    <?php endif; ?>

    <div id="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
     <p title="Adresse"><span class="icon"><img src="<?= base_path().path_to_theme(); ?>/img/location_white.svg"></span>
    
    <?php if (!empty($this->akteur->name)) : ?>
     <meta itemprop="name" content="<?= str_replace(array('"',"'"),'',$this->akteur->name); ?>" />
    <?php endif; ?>

    <?php if (!empty($this->adresse->strasse) && !empty($this->adresse->nr)) : ?>
     <span itemprop="streetAddress"><?= $this->adresse->strasse .' '. $this->adresse->nr; ?>,</span>
    <?php endif; ?>

   <?php if (!empty($this->adresse->plz)) : ?>
     <span itemprop="postalCode"><?= $this->adresse->plz; ?></span> <span itemprop="addressLocality">Leipzig</span>
   <?php endif; ?>

   <?php if (!empty($this->adresse->bezirksname)) : ?>
    <?= $this->adresse->bezirksname; ?>
   <?php endif; ?>

    </p><div class="divider"></div></div></div><?php endif; ?>

    <?php if (!empty($this->childrenEvents)) : ?>
     <div class="divider"></div>
     <ul style="padding-left:100px;font-size:0.9em;padding-bottom:15px;">
      <li style="line-height:1.7em;"><strong><?= t('Weitere Termine:'); ?></strong></li>
      <?php foreach ($this->childrenEvents as $event) : ?>
       <?php $adminFeature = ($this->isOwner ? ' class="removeAppointment" title="'.t('Termin entfernen').'" onclick="javascript:ajaxRemoveAppointment(this,'.$event->EID.');return false;"' : ''); ?>
       <li<?= $adminFeature; ?> style="line-height:1.4em;"><a href="<?= base_path().'events/?day='.$event->start->format('Y-m-d'); ?>" rel="nofollow">
       <?= $event->start->format('d.m.Y'); ?></a></li>
      <?php endforeach; ?>
      </ul>
     <?php endif; ?>
   </div>

  <?php if (isset($this->festival)) : ?>
  <div id="project-buttons" class="pcard">
   <a href="<?= base_path().$this->festival->alias; ?>" title="<?= t('Zur Festivalseite'); ?>"><button class="button"><?= t('Teil des'); ?> <?= $this->festival->name; ?></button></a>
  </div>
  <?php endif; ?>

</div>

<div id="project-content" class="large-7 large-offset-1 columns">
  <ol id="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList" class="hide-for-small-only">
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
     <a itemprop="item" href="<?= $base_url; ?>/eventprofil/<?= $this->event_id; ?>">
     <span itemprop="name" title="<?= $this->seoName; ?>"><?= $this->name; ?></span></a>
     <meta itemprop="position" content="2" />
   </li>
  </ol>

 <?php if (!empty($this->tags)) : ?>
  <aside id="eventSparten">
  <?php foreach ($this->tags as $row) : ?>
   <a href="<?= base_path(); ?>events/?filterTags[]=<?= $row->KID; ?>" rel="nofollow" title="<?= t('Zeige alle mit !kategorie getaggten Events',array('!kategorie'=>$row->kategorie)); ?>">#<?= strtolower($row->kategorie); ?></a>
  <?php endforeach; ?>
  </aside>
 <?php endif; ?>

 <div class="row collapse" id="akteurProfilTabs">
  <div class="large-12 columns">
   <ul class="tabs" data-tabs>
    <li class="tabs-title is-active"><a href="#eDesc" aria-selected="true"><?= t('Beschreibung'); ?></a></li>
    <li class="tabs-title"><a href="#eOrganizator"><?= t('Veranstalter'); ?></a></li>
   </ul>
  </div>

  <div class="large-12 columns tabs-content">

   <div class="tabs-panel is-active event-content" id="eDesc">
   <?php if(!empty($this->kurzbeschreibung)) :
         // Eventually we have to set a paragraph manually...
         $startsWithTag = (preg_match('#<(.*)>(.*?)</(.*)>#', $this->kurzbeschreibung) === 1); ?>
    <div itemprop="description"><?= (!$startsWithTag ? '<p>'.$this->kurzbeschreibung.'</p>' : $this->kurzbeschreibung); ?></div>
   <?php else : ?>
    <p><i><?= t('Hier wurde leider noch keine Beschreibung angelegt'); ?> :(</i></p>
   <?php endif; ?>	
   <?php if (!empty($this->url)) : ?>
    <br /><p><strong><?= t('Weitere Informationen'); ?>: </strong><a href="<?= $this->url; ?>" itemprop="sameAs" target="_blank"><?= $this->url; ?></a></p>
   <?php endif; ?> 
   </div>

   <div class="tabs-panel" id="eOrganizator">
    <?php if ($this->isOwner) : ?>
    <p><strong><?= t('Erstellt von'); ?>:</strong> <?= $this->ersteller; ?> <?= t('am'); ?> <?= $this->created->format('d.m.Y'); ?></p>
    <?php endif; ?>
    <?php if (empty($this->akteur)) : ?>
    <p><strong><?= t('Privater Veranstalter'); ?></strong> (<?= $this->ersteller; ?>)</p>
    <?php elseif (isset($this->festival)) : ?>
    <p><strong><?= t('Teil des'); ?> <a href="<?= base_path().$this->festival->alias; ?>" title="<?= t('Zur Festivalseite'); ?>"><?= $this->festival->name; ?></a></strong></p>
    <?php else : ?>
    <div class="large-12 columns pcard">
    <a href="<?= base_path().'akteurprofil/'.$this->akteur->AID; ?>" title="<?= t('Akteurprofil besuchen'); ?>">
     <header <?= (!empty($this->akteur->bild) ? 'style="background-image:url('.$this->akteur->bild.');"' : ''); ?>></header>
    </a>
    <a href="<?= base_path().'akteurprofil/'.$this->akteur->AID; ?>" title="<?= t('Akteurprofil besuchen'); ?>">
     <h3 id="akteurCard"><?= $this->akteur->name; ?></h3>
    </a>
    <section>
    <?php if (!empty($this->akteur->bezirk)) : ?><a href="<?= base_path().'akteurprofil/'.$this->akteur->AID; ?>" title="<?= t('Akteurprofil besuchen'); ?>"><p class="plocation"><img src="/sites/all/themes/aae_theme/img/location.svg" /><?= $this->akteur->bezirk->bezirksname; ?></p></a><?php endif; ?>
    <?php if (!empty($this->akteur->beschreibung)): ?>
     <div class="divider"></div>
     <div class="akteur-content">
      <p><?= strip_tags($this->akteur->kurzbeschreibung); ?> <a class="weiterlesen" href="<?= base_path().'akteurprofil/'.$this->akteur->AID; ?>" title="<?= t('Akteurprofil besuchen'); ?>"><?= t('... zum Akteur'); ?></a></p>
     </div>
    <?php endif; ?>
    </section>
   </div>
   <?php endif; ?>
   </div>

   </div>
  </div>
 </div><!-- /#project -->
</div><!-- /#eventprofil -->
