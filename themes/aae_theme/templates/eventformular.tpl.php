<h3>Event <?php echo ($this->target == 'update' ? 'bearbeiten' : 'anlegen'); ?></h3>
<div class="divider" style="margin-bottom: 25px;"></div>

<?php if (!$this->freigabe) : ?>
<div class="alert-box" data-alert>
 <p>Ihr Event konnte nicht gespeichert werden, da folgende Fehler vorliegen:</p><br />
 <ul>
  <?php foreach($this->fehler as $f) : ?>
    <li><?= $f; ?></li>
  <?php endforeach; ?>
 </ul>
<a href="#" class="close">&times;</a></div>
<?php endif; ?>

<form action='<?= $pathThisFile; ?>' method='POST' enctype='multipart/form-data'>

 <div class="row">

  <div class="large-4 columns">
   <label>Name <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['name']; ?>
    <input type="text" id="eventNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= $this->ph_name; ?>" required/>
   </label>
  </div>

  <div class="large-4 columns">
   <label>Eventwebsite: <?= $this->fehler['url']; ?>
    <input type="text" id="eventURLInput" name="url" value="<?= $this->url; ?>" placeholder="<?= $this->ph_url; ?>">
   </label>
  </div>

  <div class="large-4 columns">
  <label>Veranstalter:</label>
  <select name="veranstalter" id="veranstalter">
  <option value="0">Privat</option>
  <?php if (is_array($this->resultakteure) && !empty($this->resultakteure)) :
    foreach ($this->resultakteure as $akteur) : ?>
    <option value="<?= $akteur[0]->AID; ?>" <?php echo ($akteur[0]->AID == $this->veranstalter ? 'selected="selected"' : '') ?>><?= $akteur[0]->name; ?></option>
  <?php endforeach; ?>
<?php endif; ?>
 </select>
 </div>

 </div><!-- /.row -->

  <div class="row">

   <div class="large-3 columns">
    <label>Start (Datum, <span class="pflichtfeld">Pflichtfeld</span>): <?= $this->fehler['start']; ?>
     <input type="text" id="eventStartdatumInput" name="start" value="<?= $this->start; ?>" placeholder="<?= $this->ph_start; ?>" required/>
    </label>
   </div>

   <div class="large-3 columns">
    <label>Ende (Datum): <?= $this->fehler['ende']; ?>
     <input type="text" id="eventEnddatumInput" name="ende" value="<?= $this->ende; ?>" placeholder="<?= $this->ph_ende; ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>Von... (Uhrzeit): <?= $this->fehler['zeit_von']; ?>
     <input type="text" id="eventZeitvonInput" name="zeit_von" value="<?= $this->zeit_von; ?>" placeholder="<?= $this->ph_zeit_von; ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>...Bis (Uhrzeit): <?= $this->fehler['zeit_bis']; ?>
     <input type="text" id="eventZeitbisInput" name="zeit_bis" value="<?= $this->zeit_bis; ?>" placeholder="<?= $this->ph_zeit_bis; ?>">
    </label>
   </div>

  </div><!-- /.row -->

  <fieldset class="Adresse row">
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
  <label>Bezirk: <?= $this->fehler['ort']; ?>

  <select name="ort">
   <option value="" selected="selected">Bezirk auswählen</option>
   <?php foreach ($this->resultbezirke as $bezirk) : ?>
    <option value="<?= $bezirk->BID; ?>" <?php echo ($bezirk->BID == $this->ort ? 'selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
   <?php endforeach; ?>
  </select>
 </label>
 </div>

  <div class="large-4 columns">
  <label>Geodaten (Karte): <?= $this->fehler['gps']; ?>
   <input type="text" id="GPSInput" name="gps" value="<?= $this->gps; ?>" placeholder="<?= $this->ph_gps; ?>">
  </label>
</div>

</fieldset>

 <div class="row">

  <div class="large-12 columns">
  <label>Beschreibung <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['kurzbeschreibung']; ?>
   <textarea name="kurzbeschreibung" id="kurzbeschreibung" cols="45" rows="3" placeholder="<?= $this->ph_kurzbeschreibung; ?>"><?= $this->kurzbeschreibung; ?></textarea>
  </label>
  <script>CKEDITOR.replace('kurzbeschreibung', { toolbar : 'Basic' });</script>
 </div>

 </div>

  <fieldset class="row">
   <legend>Eventbild</legend>

    <input type="file" id="eventBildInput" name="bild" />

    <?php if (!empty($this->bild)) : ?>
      <input type="hidden" name="oldPic" value="<?= $this->bild; ?>" />
      <img src="<?= $this->bild; ?>" title="Bisheriges Eventbild" width=250 style="float:right;margin:8px;" />
    <?php endif; ?>

   <p><strong>Lizenzhinweis:</strong> Mit der Freigabe ihrer Daten auf leipzigerecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.</p>
 <p>Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefeldern zu beschreiben.</p><br />
 <p>Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte.</p>
 <p>Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
  </fieldset>

  <div class="row">

  <div class="large-12 columns">

    <label>Kategorien: <?= $this->fehler['sparten']; ?></label>

    <select id="eventSpartenInput" multiple="multiple" class="tokenize" name="sparten[]">
    <?php foreach ($this->sparten as $sparte) : ?>
      <?php print_r($sparte); ?>
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

  <input type="submit" class="left button" id="eventSubmit" name="submit" value="Speichern">
  <a class="secondary right button" href="<?= base_path(); ?>Events">Abbrechen</a>

 </div>
</form>
