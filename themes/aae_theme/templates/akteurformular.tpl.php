<h3>Akteur <?php echo ($this->target == 'update' ? 'bearbeiten' : 'anlegen'); ?></h3>
<?php if ($this->target == 'update') : ?>
<a href="<?= base_path(); ?>akteurloeschen/<?= $this->akteur_id; ?>" class="small secondary button round right">Löschen</a>
<?php endif; ?>
<div class="divider" style="margin-bottom: 25px;"></div>

<?php if (!$this->freigabe) : ?>
<div class="alert-box" data-alert>
 <p><strong>Akteur konnte nicht gespeichert werden, da folgende Fehler vorliegen:</strong></p><br />
 <ul>
  <?php foreach($this->fehler as $f) : ?>
    <li><?= $f; ?></li>
  <?php endforeach; ?>
 </ul>
<a href="#" class="close">&times;</a></div>
<?php endif; ?>

<form action='<?= $pathThisFile; ?>' method='POST' enctype='multipart/form-data'>

 <div class="row">
    <div class="large-6 columns">
      <?= $this->fehler['name']; ?>
      <label>Name <span class="pflichtfeld">(Pflichtfeld)</span>:
        <input type="text" id="akteurNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= $this->ph_name; ?>" required>
      </label>
    </div>

    <div class="large-6 columns">
     <label>Emailaddresse <span class="Pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['email']; ?>
      <input type="email" id="akteurEmailInput" name ="email" value="<?= $this->email; ?>" placeholder="<?= $this->ph_email; ?>" required>
     </label>
    </div>

 </div><!-- /.row -->

  <fieldset class="Adresse fieldset row">
   <legend>Adresse</legend>

   <div class="large-4 columns">
    <label>Straße: <?= $this->fehler['strasse']; ?>
     <input type="text" id="StrasseInput" name="strasse" value="<?= $this->strasse; ?>" placeholder="<?= $this->ph_strasse; ?>">
    </label>
   </div>

   <div class="large-1 columns">
    <label>Nr.: <?= $this->fehler['nr']; ?>
     <input type="text" id="NrInput" name="nr" value="<?= $this->nr; ?>" placeholder="<?= $this->ph_nr; ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>Adresszusatz: <?= $this->fehler['adresszusatz']; ?>
     <input type="text" id="AdresszusatzInput" name="adresszusatz" value="<?= $this->adresszusatz; ?>" placeholder="<?= $this->ph_adresszusatz; ?>">
    </label>
   </div>

   <div class="large-4 columns">
    <label>PLZ: <?= $this->fehler['plz']; ?>
      <input type="text" id="PLZInput" name="plz" value="<?= $this->plz; ?>" placeholder="<?= $this->ph_plz; ?>">
    </label>
   </div>

   <div class="large-4 columns">

  <label>Bezirk <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['ort']; ?>

  <select name="ort">
   <option value="" selected="selected">Bezirk auswählen</option>
   <?php foreach ($this->resultbezirke as $bezirk) : ?>
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
  <label>Geodaten (Karte): <?= $this->fehler['gps']; ?>
   <input type="text" id="GPSInput" name="gps" value="<?= $this->gps; ?>" placeholder="<?= $this->ph_gps; ?>">
  </label>
  <p id="show_coordinates" style="display:none;"><a href="#" target="_blank">Zeige Koordinaten auf Karte</a></p>
</div>

</fieldset>

<fieldset class="row fieldset">

 <legend>Sonstiges</legend>

 <div class="large-4 columns">
  <label>Öffnungszeiten: <?= $this->fehler['oeffnungszeiten']; ?>
   <input type="text" id="akteurOeffnungszeitenInput" name="oeffnungszeiten" value="<?= $this->oeffnungszeiten; ?>" placeholder="<?= $this->ph_oeffnungszeiten; ?>">
  </label>
 </div>

 <div class="large-4 columns">
  <label>Projektwebsite: <?= $this->fehler['url']; ?>
   <input type="text" id="akteurURLInput" name="url" value="<?= $this->url; ?>" placeholder="<?= $this->ph_url; ?>">
  </label>
 </div>

 <div class="large-4 columns">
  <label>Telefonnummer: <?= $this->fehler['telefon']; ?>
   <input type="text" id="akteurTelefonInput" name="telefon" value="<?= $this->telefon; ?>" placeholder="<?= $this->ph_telefon; ?>">
  </label>
 </div>

</fieldset>

<div class="row">

 <div class="large-6 columns">
  <label>Ansprechpartner: <?= $this->fehler['ansprechpartner']; ?>
   <input type="text" id="akteurAnsprechpartnerInput" name="ansprechpartner" value="<?= $this->ansprechpartner; ?>" placeholder="<?= $this->ph_ansprechpartner; ?>">
  </label>
 </div>

 <div class="large-6 columns">
  <label>Rolle des Ansprechpartners: <?= $this->fehler['funktion']; ?>
   <input type="text" id="akteurFunktionInput" name="funktion" value="<?= $this->funktion; ?>" placeholder="<?= $this->ph_funktion; ?>">
  </label>
  </div>

</div>

<div class="row">

  <div class="large-12 columns">
  <label>Beschreibung: <?= $this->fehler['beschreibung']; ?>
   <textarea name="beschreibung" id="beschreibung" cols="45" rows="3" placeholder="<?= $this->ph_beschreibung; ?>"><?= $this->beschreibung; ?></textarea>
  </label>
  <script>CKEDITOR.replace('beschreibung');</script>
 </div>

 </div>

  <fieldset class="row fieldset">
   <legend>Akteurbild</legend>

    <input type="file" id="akteurBildInput" name="bild" />

    <?php if ($this->bild != '') : ?>
      <input type="hidden" name="oldPic" value="<?= $this->bild; ?>" />
      <img src="<?= $this->bild; ?>" title="Bisheriges Profilbild" width=200 style="float:right; margin: 6px;">
    <?php endif; ?>

   <p><strong>Lizenzhinweis:</strong> Mit der Freigabe ihrer Daten auf leipzigerecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.</p>
 <p>Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefeldern zu beschreiben.</p><br />
 <p>Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte. Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
  </fieldset>

  <div class="row">

  <div class="large-12 columns">

    <label>Kategorien: <?= $this->fehler['sparten']; ?></label>

    <select id="eventSpartenInput" multiple="multiple" class="tokenize" name="sparten[]">

    <?php foreach ($this->sparten as $sparte) : ?>
     <?php if (is_array($sparte)) : ?>
     <option selected value="<?= $sparte[0]->KID; ?>"><?php echo $sparte[0]->kategorie; ?></option>
     <?php else : ?>
     <option selected value="<?= $sparte; ?>"><?= $sparte; ?></option>
     <?php endif; ?>
    <?php endforeach;?>

    <?php foreach ($this->all_sparten as $sparte) : ?>
     <option value="<?php echo $sparte->KID; ?>"><?php echo $sparte->kategorie; ?></option>
    <?php endforeach;?>
    </select>

  </div>

 </div>

 <div class="row">

  <input type="submit" class="left button" id="akteurSubmit" name="submit" value="Speichern">
  <a class="secondary right button" href="<?= base_path(); ?>Akteure">Abbrechen</a>

 </div>
</form>
