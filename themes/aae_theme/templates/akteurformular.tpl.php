<div class="row">
<h3><?= t('Akteur'); ?> <?= ($this->target == 'update' ? 'bearbeiten' : 'anlegen'); ?></h3>
<?php if ($this->target == 'update') : ?>
<a href="<?= base_path(); ?>akteurprofil/<?= $this->akteur_id; ?>/remove" class="small secondary button round right" style="margin-top:-37px;"><?= t('Löschen'); ?></a>
<?php endif; ?>
<div class="divider" style="margin-bottom: 25px;"></div>

<?php if (!empty($this->fehler)) : ?>
<div class="callout alert">
 <p><?= t('Akteur konnte nicht gespeichert werden, da folgende Fehler vorliegen:'); ?></p><br />
 <?php foreach ($this->fehler as $f) : ?>
  <p><strong><?= $f; ?></strong></p>
 <?php endforeach; ?>
</div>
<?php endif; ?>
</div>

<form action="#" method="POST" enctype="multipart/form-data">

 <div class="row">
  <div class="large-6 columns">
   <label><?= t('Name'); ?> <span class="pflichtfeld">(<?= t('Pflichtfeld'); ?>)</span>: <?= $this->fehler['name']; ?>
    <input type="text" id="akteurNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= t('Name des Vereins / der Organisation'); ?>" required />
   </label>
  </div>

  <div class="large-6 columns">
   <label><?= t('Email-Adresse'); ?> <span class="pflichtfeld">(<?= t('Pflichtfeld'); ?>)</span>: <?= $this->fehler['email']; ?>
    <input type="email" id="akteurEmailInput" name="email" value="<?= $this->email; ?>" placeholder="<?= t('E-mail Adresse'); ?>" required />
   </label>
  </div>

 </div><!-- /.row -->

  <fieldset class="Adresse fieldset large-12 columns">
   <legend><?= t('Adresse'); ?></legend>

   <div class="large-4 columns">
    <label><?= t('Straße'); ?>: <?= $this->fehler['strasse']; ?>
     <input type="text" id="StrasseInput" name="adresse[strasse]" value="<?= $this->adresse->strasse; ?>" placeholder="<?= t('Straße'); ?>" />
    </label>
   </div>

   <div class="large-1 columns">
    <label><?= t('Nr.'); ?>: <?= $this->fehler['nr']; ?>
     <input type="text" id="NrInput" name="adresse[nr]" value="<?= $this->adresse->nr; ?>" placeholder="<?= t('Hausnummer'); ?>" />
    </label>
   </div>

   <div class="large-3 columns">
    <label><?= t('Adresszusatz'); ?>: <?= $this->fehler['adresszusatz']; ?>
     <input type="text" id="AdresszusatzInput" name="adresse[adresszusatz]" value="<?= $this->adresse->adresszusatz; ?>" placeholder="<?= t('Adresszusatz'); ?>">
    </label>
   </div> 

   <div class="large-3 columns">
    <label><?= t('PLZ'); ?>: <?= $this->fehler['plz']; ?>
     <input type="text" pattern="[0-9]{5}" id="PLZInput" name="adresse[plz]" value="<?= $this->adresse->plz; ?>" placeholder="<?= t('PLZ'); ?>">
    </label>
   </div>

   <div id="akteur_access" class="switch large-1 columns">
    <input class="switch-input" id="barrierefrei" type="checkbox" name="barrierefrei"<?= ($this->barrierefrei || isset($_POST['barrierefrei']) ? ' checked="checked"' : ''); ?>>
    <label class="switch-paddle" for="barrierefrei" title="<?= t('Barrierefreier Zugang?'); ?>">
     <span class="show-for-sr"><?= t('Barrierefreier Zugang?'); ?></span>
   </label>
   </div>

   <div class="large-4 columns">

    <label><?= t('Bezirk'); ?> <span class="pflichtfeld">(<?= t('Pflichtfeld'); ?>)</span>: <?= $this->fehler['ort']; ?>

     <select name="adresse[bezirk]">
      <option value="" <?= (empty($this->adresse->bezirk) ? ' selected="selected"' : ''); ?>>- <?= t('Bezirk auswählen'); ?> -</option>
      <?php foreach ($this->allBezirke as $bezirk) : ?>
      <option value="<?= $bezirk->BID; ?>"<?= ($bezirk->BID == $this->adresse->bezirk ? ' selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
      <?php endforeach; ?>
     </select>
   </label>

  </div>

  <div class="large-4 columns">
  <label><?= t('Geodaten (Karte)'); ?>: <?= $this->fehler['gps']; ?>
   <input type="text" id="GPSInput" name="adresse[gps]" value="<?= $this->adresse->gps; ?>" placeholder="<?= t('GPS-Adresskoordinaten'); ?>">
  </label>
  <p id="show_coordinates" style="display:none;"><a href="#" target="_blank"><?= t('Zeige Koordinaten auf Karte'); ?></a></p>
</div>

</fieldset>

<div class="row">

 <div class="large-6 columns">
  <label><?= t('Ansprechpartner'); ?>: <?= $this->fehler['ansprechpartner']; ?>
   <input type="text" id="akteurAnsprechpartnerInput" name="ansprechpartner" value="<?= $this->ansprechpartner; ?>" placeholder="<?= t('Kontaktperson'); ?>">
  </label>
 </div>

 <div class="large-6 columns">
  <label><?= t('Rolle des Ansprechpartners'); ?>: <?= $this->fehler['funktion']; ?>
   <input type="text" id="akteurFunktionInput" name="funktion" value="<?= $this->funktion; ?>" placeholder="<?= t('Funktion der Kontaktperson'); ?>">
  </label>
  </div>

</div>

<div class="row">

  <div class="large-12 columns">
  <label><?= t('Beschreibungstext. In der Vorschau werden die ersten 30 Wörter angezeigt.'); ?> <?= $this->fehler['beschreibung']; ?>
   <textarea name="beschreibung" id="beschreibung" cols="45" rows="3" placeholder="<?= t('Beschreibungstext. In der Vorschau werden die ersten 30 Wörter angezeigt.'); ?>"><?= $this->beschreibung; ?></textarea>
  </label>
  <script>CKEDITOR.replace('beschreibung');</script>
 </div>

 </div>

  <div class="row" id="akteurTabs">
  <div class="medium-3 columns">
    <ul class="tabs vertical" id="example-vert-tabs" data-tabs>
      <li class="tabs-title is-active"><a href="#pbild" aria-selected="true"><?= t('Akteurbild'); ?></a></li>
      <li class="tabs-title"><a href="#pfeeds"><?= t('Feeds-Integration'); ?> <i>(Beta)</i></a></li>
      <li class="tabs-title"><a href="#psonstiges"><?= t('Sonstige Informationen'); ?></a></li>
    </ul>
    </div>
    <div class="medium-9 columns">
    <div class="tabs-content vertical" data-tabs-content="example-vert-tabs">
     <div class="tabs-panel is-active" id="pbild">
      <label for="akteurBildInput" class="button"><?= t('Bild hochladen'); ?>...</label>
      <input type="file" id="akteurBildInput" name="bild" class="show-for-sr" accept="image/*" />

      <?php if (!empty($this->bild)) : ?>
       <input type="hidden" name="oldPic" value="<?= $this->bild; ?>" />
       <div id="currentPic">
        <img src="<?= $this->bild; ?>" title="<?= t('Aktuelles Akteurbild'); ?>" />
        <a href="#"><?= t('Akteurbild löschen.'); ?></a>
       </div>
      <?php endif; ?>
      <!-- TODO: Make .licensetext editable via backend -->
      <p class="licensetext">Wir empfehlen, Bilder im <strong>Format 3:2</strong> hochzuladen (bspw. 640 x 400 Pixel)</p><br />
      <p class="licensetext"><strong>Lizenzhinweis:</strong> Mit der Freigabe ihrer Daten auf leipziger-ecken.de stimmen sie auch einer etwaigen Nutzung dieser Daten durch andere zu.</p>
      <p class="licensetext">Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten, wenn er den Urheber nennt. Wir bitten Sie, ihre Daten nach bestem (Ge-)wissen über die Eingabefeldern zu beschreiben.</p><br />
      <p class="licensetext">Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte. Bildmaterial sollte vorher abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
      <!-- END TODO -->
       </div>
      <div class="tabs-panel" id="pfeeds">

        <div class="large-12 columns">
         <p>Hier haben Sie die Möglichkeit, externe Feeds in das Profil einzubinden.</p>
         <p class="licensetext" style="padding-bottom: 8px;">Hinweis: Nicht mit diesem Profil zusammenhängende Feeds können ggf. entfernt werden. Eine Aktualisierung des Feeds erfolgt im Tagestakt.</p>
         <?php if (!empty($this->fehler['rssFeed']) || !empty($this->fehler['fbFeed']) || !empty($this->fehler['twitterFeed'])) : ?>
          <p><span class="pflichtfeld"><strong><?= t('Hinweis:'); ?> </strong><?=  $this->fehler['rssFeed'] . ' ' . $this->fehler['fbFeed'] . ' ' . $this->fehler['twitterFeed']; ?></span></p>
         <?php endif; ?>
         <label><img src="<?= base_path().path_to_theme(); ?>/img/rss.svg" style="width:17px;float:left;margin-right:10px;" /><strong>RSS-Feed</strong>
          <input type="url" name="rssFeed" placeholder="URL zum RSS-Feed (bspw. Vereinsblog)" value="<?= $this->rssFeed->url; ?>" />
         </label>
        </div>

        <div class="large-6 columns">
         <label><img src="<?= base_path().path_to_theme(); ?>/img/social-facebook-blue.svg" style="width:19px;float:left;margin-right:10px;" /><strong>Facebook-Seite</strong>
          <input type="url" name="fbFeed" placeholder="URL zur Facebook-Seite" value="<?= $this->fbFeed; ?>" />
         </label>
        </div>

        <div class="large-6 columns">
         <label><img src="<?= base_path().path_to_theme(); ?>/img/social-twitter-blue.svg" style="width:18px;float:left;margin-right:10px;" /><strong>Twitter</strong>
          <input type="text" name="twitterFeed" placeholder="Nickname (ohne @)" value="<?= $this->twitterFeed; ?>" />
         </label>
        </div>

      </div>

      <div class="tabs-panel" id="psonstiges">

        <div class="large-4 columns">
         <label><?= t('Öffnungszeiten'); ?>: <?= $this->fehler['oeffnungszeiten']; ?>
          <input type="text" id="akteurOeffnungszeitenInput" name="oeffnungszeiten" value="<?= $this->oeffnungszeiten; ?>" placeholder="<?= t('Öffnungszeiten'); ?>">
         </label>
        </div>

        <div class="large-4 columns">
         <label><?= t('Projektwebsite'); ?>: <?= $this->fehler['url']; ?>
          <input type="text" id="akteurURLInput" name="url" value="<?= $this->url; ?>" placeholder="<?= t('Website'); ?>">
         </label>
        </div>

        <div class="large-4 columns">
         <label><?= t('Telefonnummer'); ?>: <?= $this->fehler['telefon']; ?>
          <input type="text" id="akteurTelefonInput" name="telefon" value="<?= $this->telefon; ?>" placeholder="<?= t('Telefonnummer'); ?>">
         </label>
        </div>

      </div>

    </div>
  </div>
</div>

 <div class="row" style="margin-top:15px;">

  <div class="large-12 columns">

    <label><?= t('Kategorien'); ?>: <?= $this->fehler['sparten']; ?></label>
    <select id="eventSpartenInput" multiple="multiple" class="tokenize" name="tags[]">

    <?php foreach ($this->tags as $tag) : ?>
     <?php if (is_array($tag)) : ?>
     <option selected value="<?= $tag[0]->KID; ?>"><?= $tag[0]->kategorie; ?></option>
     <?php else : ?>
     <option selected value="<?= $tag->KID; ?>"><?= $tag->kategorie; ?></option>
     <?php endif; ?>
    <?php endforeach;?>

    <?php foreach ($this->allTags as $tag) : ?>
     <option value="<?= $tag->KID; ?>"><?= $tag->kategorie; ?></option>
    <?php endforeach;?>
    </select>

  </div>

 </div>
 <div class="row">
 <?php if ($this->target == 'update' && !empty($this->created)) : ?>
  <?php if ($this->created->format('d.m.Y') != '01.01.1000') : ?>
   <p style="color:grey;"><?= t('Akteur'); ?> <?= t('eingetragen am'); ?> <?= $this->created->format('d.m.Y, H:i'); ?> <?= t('Uhr'); ?>.
   <?php if ($this->modified->format('d.m.Y') != '01.01.1000' && $this->modified->format('H:i') != date('H:i')) : ?> <?= t('Zuletzt aktualisiert am'); ?> <?= $this->modified->format('d.m.Y, H:i'); ?> <?= t('Uhr'); ?>.<?php endif; ?>
   </p><div class="divider" style="margin:17px 0;"></div>
  <?php endif; ?>
 <?php endif; ?>

  <input type="submit" class="left button" id="akteurSubmit" name="submit" value="<?= t('Speichern'); ?>">
  <a class="secondary right button" href="<?= base_path(); ?>akteure" title="<?= t('Zurück zu den Akteuren'); ?>"><?= t('Abbrechen'); ?></a>
 </div>
</form>
