<h3>Event anlegen</h3>
<div class="divider" style="margin-bottom: 20px;"></div>

<?php if (!$freigabe) : ?>
<div class="alert-box" data-alert>
 <strong>Ihr Event konnte nicht gespeichert werden, da folgende Fehler vorliegen:</strong>
 <ul>
  <?php foreach($fehler as $f) : ?>
    <li><?= $f; ?></li>
  <?php endforeach; ?>
 </ul>
<a href="#" class="close">&times;</a></div>
<?php endif; ?>

<form action='<?= $pathThisFile; ?>' method='POST' enctype='multipart/form-data'>

 <div class="row">
    <div class="large-7 columns">
      <?= $fehler['name']; ?>
      <label>Name (Pflichtfeld):
        <input type="text" id="eventNameInput" name="name" value="<?= $name; ?>" placeholder="<?= $ph_name; ?>" required/>
      </label>
    </div>

<?php

if($resultakteure->rowCount() != 0) : ?>
  <div class="large-4 large-offset-1 columns">
  <label>Veranstalter:</label>
  <select name="veranstalter" size="<?= $countakteure; ?>">
  <?php foreach ($resultakteure as $akteur) : ?>
    <option value="<?= $akteur->AID; ?>"><?= $akteur->name; ?></option>
  <?php endforeach; ?>
  </select>
<?php endif; ?>
 </div>
 </div><!-- /.row -->

  <div class="row">

   <div class="large-3 columns">
    <label>Datum (Pflichtfeld): <?= $fehler['start']; ?>
     <input type="text" class="event" id="eventStartdatumInput" name="start" value="<?= $start; ?>" placeholder="<?= $ph_start; ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>Ende (Datum): <?= $fehler['ende']; ?>
     <input type="text" class="event" id="eventEnddatumInput" name="ende" value="<?= $ende; ?>" placeholder="<?= $ph_ende; ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>von (Uhrzeit; ganztägig: keine Uhrzeit angeben): <?= $fehler['zeit_von']; ?>
     <input type="text" class="event" id="eventZeitvonInput" name="zeit_von" value="<?= $zeit_von; ?>" placeholder="<?= $ph_zeit_von; ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>bis (Uhrzeit; ganztägig: keine Uhrzeit angeben): <?= $fehler['zeit_bis']; ?>
     <input type="text" class="event" id="eventZeitbisInput" name="zeit_bis" value="<?= $zeit_bis; ?>" placeholder="<?= $ph_zeit_bis; ?>">
    </label>
   </div>

  </div><!-- /.row -->

  <fieldset>
   <legend>Adresse</legend>

  <label>Straße: <?= $fehler['strasse']; ?>
   <input type="text" class="event" id="eventStrasseInput" name="strasse" value="<?= $strasse; ?>" placeholder="<?= $ph_strasse; ?>">7
  </label>

  <label>Nr.: <?= $fehler['nr']; ?>
   <input type="text" class="event" id="eventNrInput" name="nr" value="<?= $nr; ?>" placeholder="<?= $ph_nr; ?>">
  </label>

  <label>Adresszusatz: <?= $fehler['adresszusatz']; ?>
   <input type="text" class="event" id="eventAdresszusatzInput" name="adresszusatz" value="<?= $adresszusatz; ?>" placeholder="<?= $ph_adresszusatz; ?>">
  </label>

  <label>PLZ: <?= $fehler['plz']; ?>
   <input type="text" class="event" id="eventPLZInput" name="plz" value="<?= $plz; ?>" placeholder="<?= $ph_plz; ?>">
  </label>

  <label>Bezirk: <?= $fehler['ort']; ?>

  <select name="ort" size="<?= $countbezirke; ?>" >
   <option value="" selected="selected">Bezirk auswählen</option>
   <?php foreach ($resultbezirke as $bezirk) : ?>
   <option value="<?= $bezirk->BID; ?>"><?= $bezirk->bezirksname; ?></option>
   <?php endforeach; ?>
  </select>
 </label>

  <label>Geodaten: <?= $fehler['gps']; ?>
   <input type="text" class="event" id="eventGPSInput" name="gps" value="<?= $gps; ?>" placeholder="<?= $ph_gps; ?>">
  </label>

</fieldset>

  <label>Website: <?= $fehler['url']; ?>
   <input type="text" class="event" id="eventURLInput" name="url" value="<?= $url; ?>" placeholder="<?= $ph_url; ?>">
  </label>

  <label>Beschreibung: <?= $fehler['kurzbeschreibung']; ?>
   <textarea name="kurzbeschreibung" class="event" cols="45" rows="3" placeholder="<?= $ph_kurzbeschreibung; ?>"><?= $kurzbeschreibung; ?></textarea>
  </label>

  <fieldset>
   <legend>Eventbild</legend>

   <label>Bild:
    <input type="file" class="event" id="eventBildInput" name="bild" />
   </label>
   <p><strong>Lizenzhinweis:</strong> Mit der Freigabe ihrer Daten auf leipzigerecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.</p>
 <p>Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefeldern zu beschreiben.“</p>
 <p>Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte.</p>
 <p>Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
  </fieldset>

  <label>Tags: <?= $fehler['sparten']; ?>
   <input type="text" class="event" id="eventSpartenInput" name="sparten" value="<?= $sparten; ?>" placeholder="<?= $ph_sparten; ?>">
  </label>

  <input type="submit" class="left button" id="eventSubmit" name="submit" value="Speichern">
</form>
<a class="secondary right button" href="<?= base_path(); ?>Events">Abbrechen</a>
