<style type="text/css">
.region-content, #contentRow {
 width: 100% !important;
 max-width: 100% !important;
 margin: 0;
 overflow: hidden;
}
</style>

<header id="header" <?php if ($this->bild != '') : ?> style="background-image:url('<?= $this->bild; ?>');"<?php endif; ?>></header>

<div class="aaeActionBar">
 <div class="row">
  <?php if ($this->hasPermission): ?>
  <div class="large-3 large-offset-1 columns"><a href="<?= base_path(); ?>akteurprofil/<?= $this->akteur_id; ?>/edit" title="<?= t('Akteur bearbeiten'); ?>"><img src="<?= base_path().path_to_theme(); ?>/img/manage.svg" /><?= t('Bearbeiten'); ?></a></div>
  <?php endif; ?>
  <div class="large-6 columns right" style="text-align: right;">
   <a href="<?= base_path(); ?>akteurprofil/<?= $this->akteur_id; ?>/vcard_download" title="Akteur als .vcard exportieren">Export (VCard)</a>
   <a href="https://leipziger-ecken.de/contact" title="<?= t('Das Profil wurde unbefugt erstellt? Melden Sie sich hier.'); ?>" class="hide-for-small-only"><img src="<?= base_path().path_to_theme(); ?>/img/fake.svg" /><?= t('Melden'); ?></a>
   <a href="#share" class="popup-link" title="Akteursseite in den sozialen Netzwerken posten"><img src="<?= base_path().path_to_theme(); ?>/img/share.svg" /><?= t('Teilen'); ?></a>
   <div id="share" class="popup large-3 columns">
     <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php global $base_url;
 echo $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen', array('!network' => 'Twitter')); ?>" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>
     <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen', array('!network' => 'Facebook')); ?>" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>
     <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="<?= t('Auf !network teilen', array('!network' => 'Google+')); ?>" class="g_plus button"><img alt="Google+" src="<?= base_path().path_to_theme(); ?>/img/social-googleplus-outline.svg"><span></span></a>
     <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $this->name; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="<?= t('Auf !network teilen', array('!network' => 'Diaspora / Friendica')); ?>"><img alt="Federated networks" src="<?= base_path().path_to_theme(); ?>/img/social-diaspora.svg"></a>
   </div>
  </div>
 </div>
</div><!-- /.aaeActionBar -->

<div id="project" class="row" itemscope itemtype="http://schema.org/Organization">
 <?php global $base_root; ?>
 <meta itemprop="name" content="<?= $this->name; ?>" />
 <meta itemprop="url" content="<?= $base_root.'/akteurprofil/'.$this->akteur_id; ?>" />

 <aside class="left large-4 columns">

  <div class="pcard">
   <header<?= (!empty($this->bild) ? ' style="background-image:url('.$this->bild.');"' : ''); ?>>
  	<?php if (!empty($this->bild)) echo '<img src="'.$this->bild.'" style="visbility:hidden;" itemprop="logo" alt="'. t('Profilbild von !username', array('!username' => $this->name)) .'"/>';
	        else echo '<img src="'.base_path().path_to_theme().'/img/project_bg.png" style="visibility:hidden;" />';	?>
	 </header>
	</div>

	<div id="project-info" class="pcard">
	<?php if (!empty($this->oeffnungszeiten)) : ?>
	 <p title="<?= t('Öffnungszeiten'); ?>"><span class="icon"><img src="<?= base_path().path_to_theme(); ?>/img/clock_white.svg" /></span><?= $this->oeffnungszeiten; ?></p>
	 <div class="divider"></div>
	<?php endif; ?>

	 <!-- TODO: Zu ergänzen mit "Bezirk" in strong-case's -->
  <?php if (!empty($this->adresse->strasse) || !empty($this->adresse->plz)) : ?>
   <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
	 <p title="<?= t('Adresse'); ?>"><span class="icon"><img src="<?= base_path().path_to_theme(); ?>/img/location_white.svg" /></span>
   <span itemprop="streetAddress"><?= $this->adresse->strasse; ?> <?= $this->adresse->nr; ?></span><br />
   <?php if (!empty($this->adresse->plz)) : ?><span itemprop="postalCode"><?= $this->adresse->plz; ?></span> <span itemprop="addressLocality">Leipzig</span></p><?php endif; ?>
   </div><div class="divider"></div>
  <?php endif; ?>

  <?php if (!empty($this->url) && $this->url != 'http://') : ?>
	 <p title="<?= t('Projektwebsite'); ?>"><span class="icon"><img src="<?= base_path().path_to_theme(); ?>/img/cloud_white.svg" /></span><a href="<?= $this->url; ?>" itemprop="sameAs" target="_blank"><?= str_replace('http://', '', $this->url);?></a></p>
	 <div class="divider"></div>
	<?php endif; ?>

  <?php if ($this->barrierefrei == '1') : ?>
  <p title="<?= t('Barrierefreier Zugang'); ?>"><span class="icon" style="padding:7px 10px;"><img style="width:15px;" src="<?= base_path().path_to_theme(); ?>/img/accessibility_icon_white.svg" /></span><?= t('Barrierefreier Zugang'); ?></p>
  <div class="divider"></div>
  <?php endif; ?>

  <?php if (!empty($this->twitterFeed)) : ?>
  <p title="<?= t('@!user auf Twitter',array('!user'=>$this->twitterFeed)); ?>"><span class="icon twitter-icon"><img src="<?= base_path().path_to_theme(); ?>/img/social-twitter-blue.svg" /></span><a href="https://twitter.com/<?= $this->twitterFeed; ?>" target="_blank"><?= t('@!user auf Twitter',array('!user'=>$this->twitterFeed)); ?></a></p>
 	<div class="divider"></div>
  <?php endif; ?>
  
  <?php if (!empty($this->fbFeed)) : ?>
  <p title="<?= t('Akteur auf Twitter'); ?>"><span class="icon fb-icon"><img src="<?= base_path().path_to_theme(); ?>/img/social-facebook-blue.svg" /></span><a href="<?= $this->fbFeed; ?>" target="_blank"><?= t('Akteur auf Facebook'); ?></a></p>
  <div class="divider"></div>
  <?php endif; ?>

	<?php if ($this->showMap) : ?>
	<div id="map" style="width: 100%; height: 180px;"></div>
	<?php endif; ?>

	</div>

  <div id="project-buttons" class="pcard">
   <a href="#"><button id="akteur-contact" class="button"><?= t('Kontaktieren'); ?></button></a>
   <?php if (!empty($this->resultFestivals) && is_array($this->resultFestivals)) :
    foreach ($this->resultFestivals as $festival) : ?>
   <a href="<?= $base_root .'/'. $festival->alias; ?>"><button class="festival button" style="background:#fff;margin-top:2px;color:#2199e8;">
   <?= ($festival->admin == $this->akteur_id ? t('Veranstalter') : t('Teilnehmer')); ?> <?= t('des'); ?> <?= $festival->name; ?></button></a>
    <?php endforeach; ?>
   <?php endif; ?>
   <?php if ($this->hasPermission): ?>
   <a href="<?= $base_root; ?>/events/new"><button class="festival button" style="background:#fff;margin-top:2px;color:#2199e8;"><?= t('Event zum Akteur hinzufügen'); ?></button></a>
   <?php endif; ?>
  </div>

 </aside>
 <section id="project-content" class="large-7 large-offset-1 columns">

  <ol id="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
   <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
    <a itemprop="item" href="<?= $base_url; ?>">
    <span itemprop="name" title="<?= t('Startseite'); ?>"><?= t('Startseite'); ?></span></a>
    <meta itemprop="position" content="0" />
   </li>
   <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
    <a itemprop="item" href="<?= $base_url; ?>/akteure">
    <span itemprop="name" title="<?= t('Akteure'); ?>"><?= t('Akteure'); ?></span></a>
    <meta itemprop="position" content="1" />
   </li>
   <li id="activeEvent" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
    <a itemprop="item" href="<?= $base_url; ?>/akteurprofil/<?= $this->akteur_id; ?>">
    <span itemprop="name" title="<?= t('Akteurprofil von !username', array('!username' => $this->name)); ?>"><?= $this->name; ?></span></a>
    <meta itemprop="position" content="2" />
   </li>
  </ol>

  <?php if (!empty($this->tags)) : ?>
  <aside id="akteurSparten">
  <?php foreach ($this->tags as $tag) : ?>
   <a href="<?= base_path(); ?>akteure/?filterTags[]=<?= $tag->KID; ?>" rel="nofollow" title="<?= t('Zeige alle mit !kategorie getaggten Akteure', array('!kategorie' => $tag->kategorie)); ?>">#<?= strtolower($tag->kategorie); ?></a>
  <?php endforeach; ?>
  </aside>
  <?php endif; ?>

  <div class="row collapse" id="akteurProfilTabs">
   <div class="large-12 columns">
    <ul class="tabs" data-tabs>
     <li class="tabs-title is-active"><a href="#pdesc" aria-selected="true"><?= t('Beschreibung'); ?></a></li>
     <?php if (!empty($this->events)) : ?><li class="tabs-title"><a href="#pevents"><?= t('Veranstaltungen'); ?></a></li><?php endif; ?>
     <?php if (!empty($this->rssFeed)) : ?><li class="tabs-title"><a href="#prss"><?= t('RSS-Feed'); ?></i></a></li><?php endif; ?>
    </ul>
   </div> 

   <div class="large-12 columns tabs-content">

    <div class="tabs-panel is-active" id="pdesc">
     <?php if (!empty($this->beschreibung)): ?>
     <div class="akteur-content">
      <p itemprop="description"><?= $this->beschreibung; ?></p>
     </div>
     <?php else : ?>
      <p><i><?= t('Hier wurde leider noch keine Beschreibung angelegt'); ?> :(</i></p>
     <?php endif; ?>

	 
    </div>

    <?php if (!empty($this->events)) : ?>
    <div class="tabs-panel" id="pevents">
     <div id="next-events">

     <?php foreach (array_reverse($this->events) as $event) : ?>
     <?php $isOutdated = ($event->start->format('Ymd') < date('Ymd') ? true : false); ?>
      <div class="aaeEvent row<?= ($isOutdated ? ' outdated' : ''); ?>">
       <div class="date large-2 columns button secondary"><?= $event->start->format('d'); ?><br /><?= $this->monat_short[$event->start->format('m')]; ?></div>
       <div class="content large-10 columns">
        <p><a style="line-height:1.6em;" href="<?= base_path(); ?>eventprofil/<?= $event->EID; ?>"> <strong><?= $event->name; ?></strong></a>
        <span class="right"><?= ($event->ende->format('H:i') !== '00:00' ? $event->start->format('H:i') : ''); ?><?= ($event->ende->format('H:i') !== '00:00' ? ' - '. $event->ende->format('H:i') : ''); ?></span></p>
         <?php if (!empty($event->kurzbeschreibung)): ?>
         <div class="divider"></div>
         <?php $numwords = 30; preg_match("/(\S+\s*){0,$numwords}/", $event->kurzbeschreibung, $regs); ?>
         <p><?= strip_tags(trim($regs[0])); ?></p>
         <p><a href="<?= base_path().'eventprofil/'.$event->EID; ?>">... <?= t('zum Event'); ?></a></p>      
         <?php endif; ?>
       </div>
      </div>
      <?php endforeach; ?>
     </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->rssFeed)) : ?>
    <div class="tabs-panel" id="prss">
     <?php foreach ($this->rssFeed as $feed) : ?>
     <div class="rssitem">
      <h5><a href="<?= $feed->link; ?>"><?= $feed->title; ?></a></h5>
      <p><?= $feed->description; ?></p>
     </div>
     <?php endforeach; ?>

     <a href="<?= $this->rssFeedUrl; ?>" class="secondary hollow button"><?= t('Gesamten Feed öffnen'); ?></a>
   </div>
    <?php endif; ?>

  </div>

 </section>
</div>
