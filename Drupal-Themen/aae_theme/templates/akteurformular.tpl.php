<h3>Akteur <?php echo ($this->target == 'update' ? 'bearbeiten' : 'anlegen'); ?></h3>
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
    <div class="large-7 columns">
      <?= $this->fehler['name']; ?>
      <label>Name <span class="pflichtfeld">(Pflichtfeld)</span>:
        <input type="text" id="akteurNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= $this->ph_name; ?>" required/>
      </label>
    </div>
 </div><!-- /.row -->

  <div class="row">


  </div><!-- /.row -->

  <fieldset class="eventAdresse row">
   <legend>Adresse</legend>

   <div class="large-4 columns">
    <label>Straße: <?= $this->fehler['strasse']; ?>
     <input type="text" id="akteurStrasseInput" name="strasse" value="<?= $this->strasse; ?>" placeholder="<?= $this->ph_strasse; ?>">
    </label>
   </div>

   <div class="large-1 columns">
    <label>Nr.: <?= $this->fehler['nr']; ?>
     <input type="text" id="akteurNrInput" name="nr" value="<?= $this->nr; ?>" placeholder="<?= $this->ph_nr; ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>Adresszusatz: <?= $this->fehler['adresszusatz']; ?>
     <input type="text" id="akteurAdresszusatzInput" name="adresszusatz" value="<?= $this->adresszusatz; ?>" placeholder="<?= $this->ph_adresszusatz; ?>">
    </label>
   </div>

   <div class="large-4 columns">
    <label>PLZ: <?= $this->fehler['plz']; ?>
      <input type="text" id="akteurPLZInput" name="plz" value="<?= $this->plz; ?>" placeholder="<?= $this->ph_plz; ?>">
    </label>
   </div>

   <div class="large-4 columns">

  <label>Bezirk: <?= $this->fehler['ort']; ?>

  <select name="ort">
   <option value="" selected="selected">Bezirk auswählen</option>
   <?php foreach ($this->resultbezirke as $bezirk) : ?>
   <option value="<?= $bezirk->BID; ?>"><?= $bezirk->bezirksname; ?></option>
   <?php endforeach; ?>
  </select>
 </label>
 </div>

  <div class="large-4 columns">
  <label>Geodaten (Karte): <?= $this->fehler['gps']; ?>
   <input type="text" id="akteurGPSInput" name="gps" value="<?= $this->gps; ?>" placeholder="<?= $this->ph_gps; ?>" disabled>
  </label>
</div>

</fieldset>

<label>Öffnungszeiten: <?= $this->fehler['oeffnungszeiten']; ?>
 <input type="text" id="akteurOeffnungszeitenInput" name="oeffnungszeiten" value="<?= $this->oeffnungszeiten; ?>" placeholder="<?= $this->ph_oeffnungszeiten; ?>">
</label>

<label>Ansprechpartner: <?= $this->fehler['ansprechpartner']; ?>
 <input type="text" id="akteurAnsprechpartnerInput" name="ansprechpartner" value="<?= $this->ansprechpartner; ?>" placeholder="<?= $this->ph_ansprechpartner; ?>">
</label>

<label>Rolle des Ansprechpartners: <?= $this->fehler['funktion']; ?>
 <input type="text" id="akteurFunktionInput" name="funktion" value="<?= $this->funktion; ?>" placeholder="<?= $this->ph_funktion; ?>">
</label>

<label>Telefonnummer: <?= $this->fehler['telefon']; ?>
 <input type="text" id="akteurTelefonInput" name="telefon" value="<?= $this->telefon; ?>" placeholder="<?= $this->ph_telefon; ?>">
</label>

<label>Emailaddresse <span class="Pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['email']; ?>
<input type="email" id="akteurEmailInput" name ="email" value="<?= $this->email; ?>" placeholder="<?= $this->ph_email; ?>">
</label>

 <div class="row">

  <div class="large-12 columns">

   <label>Projektwebsite: <?= $this->fehler['url']; ?>
    <input type="text" id="akteurURLInput" name="url" value="<?= $this->url; ?>" placeholder="<?= $this->ph_url; ?>">
   </label>
  </div>

  <div class="large-12 columns">
  <label>Beschreibung: <?= $this->fehler['beschreibung']; ?>
   <textarea name="beschreibung" cols="45" rows="3" placeholder="<?= $this->ph_beschreibung; ?>"><?= $this->beschreibung; ?></textarea>
  </label>
 </div>

 </div>

  <fieldset class="row">
   <legend>Bild</legend>

    <input type="file" id="akteurBildInput" name="bild" />

   <p><strong>Lizenzhinweis:</strong> Mit der Freigabe ihrer Daten auf leipzigerecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.</p>
 <p>Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefeldern zu beschreiben.</p><br />
 <p>Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte.</p>
 <p>Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
  </fieldset>

  <div class="row">

  <div class="large-12 columns">
  <label>Tags: <?= $this->fehler['sparten']; ?>
   <input type="text" id="akteurSpartenInput" name="sparten" value="<?= $this->sparten; ?>" placeholder="<?= $this->ph_sparten; ?>">
  </label>
  </div>

 </div>

 <div class="row">

  <input type="submit" class="left button" id="akteurSubmit" name="submit" value="Speichern">
  <a class="secondary right button" href="<?= base_path(); ?>Akteure">Abbrechen</a>

 </div>
</form>
