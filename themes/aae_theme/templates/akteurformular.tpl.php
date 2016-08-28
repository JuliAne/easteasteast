<div class="row">
<h3><?= t('Akteur'); ?> <?php echo ($this->target == 'update' ? 'bearbeiten' : 'anlegen'); ?></h3>
<?php if ($this->target == 'update') : ?>
<a href="<?= base_path(); ?>akteurprofil/<?= $this->akteur_id; ?>/remove" class="small secondary button round right" style="margin-top:-37px;"><?= t('Löschen'); ?></a>
<?php endif; ?>
<div class="divider" style="margin-bottom: 25px;"></div>

<?php if (!$this->freigabe) : ?>
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
      <?= $this->fehler['name']; ?>
      <label>Name <span class="pflichtfeld">(<?= t('Pflichtfeld'); ?>)</span>:
        <input type="text" id="akteurNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= t('Name des Vereins / der Organisation'); ?>" required />
      </label>
    </div>

    <div class="large-6 columns">
     <label>Email-Adresse <span class="pflichtfeld">(<?= t('Pflichtfeld'); ?>)</span>: <?= $this->fehler['email']; ?>
      <input type="email" id="akteurEmailInput" name="email" value="<?= $this->email; ?>" placeholder="<?= t('E-mail Adresse'); ?>" required />
     </label>
    </div>

 </div><!-- /.row -->

  <fieldset class="Adresse fieldset row">
   <legend><?= t('Adresse'); ?></legend>

   <div class="large-4 columns">
    <label><?= t('Straße'); ?>: <?= $this->fehler['strasse']; ?>
     <input type="text" id="StrasseInput" name="strasse" value="<?= $this->strasse; ?>" placeholder="<?= t('Straße'); ?>" />
    </label>
   </div>

   <div class="large-1 columns">
    <label><?= t('Nr.'); ?>: <?= $this->fehler['nr']; ?>
     <input type="text" id="NrInput" name="nr" value="<?= $this->nr; ?>" placeholder="<?= t('Hausnummer'); ?>" />
    </label>
   </div>

   <div class="large-3 columns">
    <label><?= t('Adresszusatz'); ?>: <?= $this->fehler['adresszusatz']; ?>
     <input type="text" id="AdresszusatzInput" name="adresszusatz" value="<?= $this->adresszusatz; ?>" placeholder="<?= t('Adresszusatz'); ?>">
    </label>
   </div> 

   <div class="large-3 columns">
    <label><?= t('PLZ'); ?>: <?= $this->fehler['plz']; ?>
      <input type="text" pattern="[0-9]{5}" id="PLZInput" name="plz" value="<?= $this->plz; ?>" placeholder="<?= t('PLZ'); ?>">
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

  <select name="ort">
   <option value="" selected="selected">- <?= t('Bezirk auswählen'); ?> -</option>
   <?php foreach ($this->allBezirke as $bezirk) : ?>
    <?php if ($bezirk->BID == $this->ort) : ?>
    <option value="<?= $bezirk->BID; ?>" selected="selected"><?= $bezirk->bezirksname; ?></option>
    <?php else : ?>
    <option value="<?= $bezirk->BID; ?>"><?= $bezirk->bezirksname; ?></option>
    <?php endif; ?>
   <?php endforeach; ?>
  </select>
  </label>
 </div>

  <div class="large-4 columns">
  <label><?= t('Geodaten (Karte)'); ?>: <?= $this->fehler['gps']; ?>
   <input type="text" id="GPSInput" name="gps" value="<?= $this->gps; ?>" placeholder="<?= t('GPS-Adresskoordinaten'); ?>">
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
      <li class="tabs-title"><a href="#prss">RSS-Integration <i>(Beta)</i></a></li>
      <li class="tabs-title"><a href="#psonstiges"><?= t('Sonstige Informationen'); ?></a></li>
    </ul>
    </div>
    <div class="medium-9 columns">
    <div class="tabs-content vertical" data-tabs-content="example-vert-tabs">
     <div class="tabs-panel is-active" id="pbild">
      <label for="akteurBildInput" class="button"><?= t('Bild hochladen'); ?>...</label>
      <input type="file" id="akteurBildInput" name="bild" class="show-for-sr" />

        <?php if (!empty($this->bild)) : ?>
          <input type="hidden" name="oldPic" value="<?= $this->bild; ?>" />
          <div id="currentPic">
           <img src="<?= $this->bild; ?>" title="<?= t('Aktuelles Akteurbild'); ?>" />
           <a href="#"><?= t('Akteurbild löschen.'); ?></a>
          </div>
        <?php endif; ?>
      <p class="licensetext">Wir empfehlen, Bilder im <strong>Format 3:2</strong> hochzuladen (bspw. 640 x 400 Pixel)</p><br />
      <p class="licensetext"><strong>Lizenzhinweis:</strong> Mit der Freigabe ihrer Daten auf leipziger-ecken.de stimmen sie auch einer etwaigen Nutzung dieser Daten durch andere zu.</p>
      <p class="licensetext">Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten, wenn er den Urheber nennt. Wir bitten Sie, ihre Daten nach bestem (Ge-)wissen über die Eingabefeldern zu beschreiben.</p><br />
      <p class="licensetext">Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte. Bildmaterial sollte vorher abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>

       </div>
      <div class="tabs-panel" id="prss">
        <img src="<?= base_path().path_to_theme(); ?>/img/rss.svg" style="width:18px;float:left;margin-right:10px;" />
        <p>Hier haben Sie die Möglichkeit, einen bestehenden <strong>RSS-Feed</strong> (bspw. ihres Blogs) in das Profil einzubinden.</p>
        <p class="licensetext" style="padding-bottom: 8px;">Hinweis: Nicht mit diesem Profil zusammenhängende Feeds können ggf. entfernt werden. Eine Aktualisierung des Feeds erfolgt im Tagestakt.</p>
        <input type="url" name="rssFeed" placholder="URL zum RSS-Feed" value="<?= $this->rssFeed->url; ?>" />
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

    <select id="eventSpartenInput" multiple="multiple" class="tokenize" name="sparten[]">

    <?php foreach ($this->sparten as $sparte) : ?>
     <?php if (is_array($sparte)) : ?>
     <option selected value="<?= $sparte[0]->KID; ?>"><?= $sparte[0]->kategorie; ?></option>
     <?php else : ?>
     <option selected value="<?= $sparte; ?>"><?= $sparte; ?></option>
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
   <p style="color:grey;"><?= t('Akteur'); ?> <?= t('eingetragen am'); ?> <?= $this->created->format('d.m.Y, H:i'); ?> Uhr.
   <?php if ($this->modified->format('d.m.Y') != '01.01.1000' && $this->modified->format('H:i') != date('H:i')) : ?> <?= t('Zuletzt aktualisiert am'); ?> <?= $this->modified->format('d.m.Y, H:i'); ?> <?= t('Uhr'); ?>.<?php endif; ?>
   </p><div class="divider" style="margin:17px 0;"></div>
  <?php endif; ?>
 <?php endif; ?>

  <input type="submit" class="left button" id="akteurSubmit" name="submit" value="<?= t('Speichern'); ?>">
  <a class="secondary right button" href="<?= base_path(); ?>akteure" title="<?= t('Zurück zu den Akteuren'); ?>"><?= t('Abbrechen'); ?></a>
 </div>
</form>
