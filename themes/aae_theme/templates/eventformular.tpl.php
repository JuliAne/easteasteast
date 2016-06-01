<?php $recurringEventTypes = array(
   '2' => t('Wöchentliche Wiederholung'),
   '3' => t('2-wöchentliche Wiederholung'),
   '4' => t('Monatliche Wiederholung'),
   '5' => t('2-monatliche Wiederholung')
  ); ?>
<div class="row">
 <h3>Event <?= ($this->target == 'update' ? t('bearbeiten') : t('anlegen')); ?></h3>
 <?php if ($this->target == 'update') : ?>
 <a href="<?= base_path(); ?>eventprofil/<?= $this->event_id; ?>/remove" class="small secondary button round right" style="margin-top:-37px;" title="<?= t('Event löschen?'); ?>"><?= t('Löschen'); ?></a>
 <?php endif; ?>
 <div class="divider" style="margin-bottom: 25px;"></div>

 <?php if (!$this->freigabe) : ?>
 <div class="callout alert">
  <p><?= t('Ihr Event konnte nicht gespeichert werden, da folgende Fehler vorliegen:'); ?></p><br />
  <?php foreach($this->fehler as $f) : ?>
   <p><strong><?= $f; ?></strong></p>
  <?php endforeach; ?>
  </div>
 <?php endif; ?>
</div>

<form action="#" method="POST" enctype="multipart/form-data" class="row">

  <div class="large-4 columns">
   <label><?= t('Name'); ?> <span class="pflichtfeld">(<?= t('Pflichtfeld'); ?>)</span>: <?= $this->fehler['name']; ?>
    <input type="text" id="eventNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= t('Veranstaltungsname'); ?>" required/>
   </label>
  </div>

  <div class="large-4 columns">
   <label><?= t('Eventwebsite'); ?>: <!--<?= $this->fehler['url']; ?>-->
    <input type="url" id="eventURLInput" name="url" value="<?= $this->url; ?>" placeholder="<?= t('URL'); ?>">
   </label>
  </div>

  <div class="large-4 columns">
  <label><?= t('Veranstalter'); ?>:</label>
  <select name="veranstalter" id="veranstalter">
  <option value="0"><?= t('Privat'); ?></option>
  <?php if (is_array($this->resultakteure) && !empty($this->resultakteure)) :
    foreach ($this->resultakteure as $akteur) : ?>
    <option value="<?= $akteur[0]->AID; ?>" <?php echo ($akteur[0]->AID == $this->veranstalter ? 'selected="selected"' : '') ?>><?= $akteur[0]->name; ?></option>
   <?php endforeach; ?>
  <?php endif; ?>
  </select>
  </div>

  <div class="row">
   <fieldset class="fieldset">
   <legend><?= t('Datum'); ?></legend>

   <div class="large-3 columns">
    <label>Start (Datum, <span class="pflichtfeld">Pflichtfeld</span>): <?= $this->fehler['start']; ?>
     <input type="date" id="eventStartdatumInput" name="start" value="<?= $this->start; ?>" placeholder="<?= t("Starttag")." (yyyy-mm-dd)"; ?>" required/>
    </label>
   </div>

   <div class="large-3 columns">
    <label>Ende (Datum): <?= $this->fehler['ende']; ?>
     <input type="date" id="eventEnddatumInput" name="ende" value="<?= ($this->ende != '1000-01-01' ? $this->ende : ''); ?>" placeholder="<?= t("Endtag")." (yyyy-mm-dd)"; ?>">
    </label>
   </div>

   <div class="large-2 columns">
    <label>Von... (Uhrzeit): <?= $this->fehler['zeit_von']; ?>
     <input type="text" id="eventZeitvonInput" name="zeit_von" value="<?= ($this->hat_zeit_von ? $this->zeit_von : ''); ?>" placeholder="<?= t("Uhrzeit: hh:mm"); ?>">
    </label>
   </div>

   <div class="large-2 columns">
    <label>...Bis (Uhrzeit): <?= $this->fehler['zeit_bis']; ?>
     <input type="text" id="eventZeitbisInput" name="zeit_bis" value="<?= ($this->hat_zeit_bis ? $this->zeit_bis : ''); ?>" placeholder="<?= t("Uhrzeit: hh:mm"); ?>" data-zdp_pair="#eventRecurresTill">
    </label>
   </div>

   <div class="switch large-2 columns" style="text-align:right;">
    <input class="switch-input" id="eventRecurres" type="checkbox" name="eventRecurres"<?= ($this->eventRecurres || isset($_POST['eventRecurres']) ? ' checked="checked"' : ''); ?>>
    <label class="switch-paddle" for="eventRecurres" title="<?= t('Sich wiederholendes Event?'); ?>">
    <span class="show-for-sr"><?= t('Sich wiederholendes Event?'); ?></span>
   </div>

   <div id="eventRecurresData" class="large-12 columns"<?= (!empty($this->recurringEventType) || isset($_POST['eventRecurres']) ? '' : ' style="display:none;"'); ?>>

    <p class="large-12 columns licensetext"><strong>Wiederkehrende Veranstaltung.</strong> Beta-Feature! Setzt die Termine für einen abfolgenden Events-Rhytmus. Einzelne Termine auf der Eventseite entfernt werden.</p>

    <div class="large-4 left columns">
     <label><?= t('Rhytmus:'); ?> <?= $this->fehler['eventRecurres']; ?>

     <select id="eventRecurringType" name="eventRecurringType">
     <?php foreach ($recurringEventTypes as $key => $value) : ?>
      <option value="<?= $key; ?>" <?= ($key == $this->recurringEventType || $key == $_POST['eventRecurringType'] ? 'selected="selected"' : '') ?>><?= $value; ?></option>
     <?php endforeach; ?>
     </select></label>
    </div>

    <div class="large-4 columns" style="float:left !important;">
     <label><?= t('Bis max. zum:'); ?> <?= $this->fehler['eventRecurresTill']; ?>
      <input type="date" id="eventRecurresTill" name="eventRecurresTill" value="<?= ($this->eventRecurresTill != '1000-01-01' ? $this->eventRecurresTill : ''); ?>" placeholder="<?= t("Endtag")." (yyyy-mm-dd)"; ?>">
     </label>
    </div>

   </div>

   </fieldset>
  </div><!-- /.row -->

  <div class="row">
  <fieldset class="Adresse fieldset">
   <legend>Adresse</legend>

   <div class="large-4 columns">
    <label>Straße: <?= $this->fehler['strasse']; ?>
     <input type="text" id="StrasseInput" name="strasse" value="<?= $this->strasse; ?>" placeholder="<?= t("Strasse"); ?>">
    </label>
   </div>

   <div class="large-1 columns">
    <label>Nr.: <?= $this->fehler['nr']; ?>
     <input type="text" id="NrInput" name="nr" value="<?= $this->nr; ?>" placeholder="<?= t("Hausnummer"); ?>">
    </label>
   </div>

   <div class="large-3 columns">
    <label>Adresszusatz: <?= $this->fehler['adresszusatz']; ?>
     <input type="text" id="AdresszusatzInput" name="adresszusatz" value="<?= $this->adresszusatz; ?>" placeholder="<?= t("Adresszusatz"); ?>">
    </label>
   </div>

   <div class="large-4 columns">
    <label>PLZ: <?= $this->fehler['plz']; ?>
      <input type="text" pattern="[0-9]{5}" id="PLZInput" name="plz" value="<?= $this->plz; ?>" placeholder="<?= t("PLZ"); ?>">
    </label>
   </div>

   <div class="large-4 columns">
  <label>Bezirk: <?= $this->fehler['ort']; ?>

  <select name="ort">
   <option value="" selected="selected"><?= t('Bezirk auswählen'); ?></option>
   <?php foreach ($this->resultbezirke as $bezirk) : ?>
    <option value="<?= $bezirk->BID; ?>" <?php echo ($bezirk->BID == $this->ort ? 'selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
   <?php endforeach; ?>
  </select>
 </label>
 </div>

  <div class="large-4 columns">
  <label>Geodaten (Karte): <?= $this->fehler['gps']; ?>
   <input type="text" id="GPSInput" name="gps" value="<?= $this->gps; ?>" placeholder="<?= t("GPS Koordinaten"); ?>">
  </label>
  <p id="show_coordinates" style="display:none;"><a href="#" target="_blank"><?= t('Zeige Koordinaten auf Karte'); ?></a></p>
</div>

</fieldset></div>

 <div class="row">

  <div class="large-12 columns">
  <label>Beschreibung. In der Vorschau erscheinen die ersten 30 Wörter <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['kurzbeschreibung']; ?>
   <textarea name="kurzbeschreibung" id="kurzbeschreibung" cols="45" rows="3" placeholder="<?= t("Beschreibungstext"); ?>"><?= $this->kurzbeschreibung; ?></textarea>
  </label>
  <script>CKEDITOR.replace('kurzbeschreibung', { toolbar : 'Basic' });</script>
 </div>

 </div>

 <div class="row">
  <fieldset class="fieldset">
   <legend>Eventbild</legend>

    <label for="eventBildInput" class="button"><?= t('Bild hochladen'); ?>...</label>
    <input type="file" id="eventBildInput" name="bild" class="show-for-sr" />

    <?php if (!empty($this->bild)) : ?>
    <input type="hidden" name="oldPic" value="<?= $this->bild; ?>" />
    <div id="currentPic">
     <img src="<?= $this->bild; ?>" title="<?= t('Aktuelles Eventbild'); ?>" />
     <a href="#"><?= t('Eventbild löschen.'); ?></a>
    </div>
    <?php endif; ?>
    <p class="licensetext">Wir empfehlen, Bilder im <strong>Format 3:2</strong> hochzuladen (bspw. 640 x 400 Pixel)</p><br />
    <p class="licensetext"><strong>Lizenzhinweis:</strong> Mit der Freigabe ihrer Daten auf Leipziger-Ecken.de stimmen sie auch einer Nutzung ihrer angezeigten Daten durch andere zu.</p>
    <p class="licensetext">Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten sie ihre Daten nach besten Wissen und Gewissen über die Eingabefelder zu beschreiben.</p><br />
    <p class="licensetext">Wir übernehmen keinerlei Haftung für Schadensersatzforderung etc. in Bezug auf Dritte.</p>
    <p class="licensetext">Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
   </fieldset>
  </div>

  <div class="row">
   <div class="large-12 columns">

    <label><?= t('Kategorien:'); ?> <?= $this->fehler['sparten']; ?></label>

    <select id="eventSpartenInput" multiple="multiple" class="tokenize" name="sparten[]">
      
    <?php if (!empty($this->sparten)) : ?>
    <?php foreach ($this->sparten as $sparte) : ?>
     <?php if (is_array($sparte)) : ?>
      <option selected value="<?= $sparte[0]->KID; ?>"><?php echo $sparte[0]->kategorie; ?></option>
     <?php else : ?>
     <?= var_dump($sparte); ?>
      <option selected value="<?= $sparte->KID; ?>"><?= $sparte->kategorie; ?></option>
      <?php endif; ?>
    <?php endforeach;?>
    <?php endif; ?>

    <?php foreach ($this->all_sparten as $sparte) : ?>
     <option value="<?php echo $sparte->KID; ?>"><?php echo $sparte->kategorie; ?></option>
    <?php endforeach;?>
    </select>

  </div>

 <div class="row">
 <?php if ($this->target == 'update' && !empty($resultEvent->created)) : ?>
  <?php if ($this->created->format('d.m.Y') != '01.01.1000') : ?>
  <p style="color:grey;">Event eingetragen am <?= $this->created->format('d.m.Y, H:i'); ?> Uhr.
  <?php if ($this->modified->format('d.m.Y') != '01.01.1000' && $this->modified->format('H:i') != date('H:i')) : ?> Zuletzt aktualisiert am <?= $this->modified->format('d.m.Y, H:i'); ?> Uhr.<?php endif; ?>
  </p><div class="divider" style="margin:17px 0;"></div>
  <?php endif; ?>
 <?php endif; ?>

  <input type="submit" class="left button" id="eventSubmit" name="submit" value="<?= t('Speichern'); ?>">
  <a class="secondary right button" href="<?= base_path(); ?>events" title="<?= t('Zurück zu den Events'); ?>"><?= t('Abbrechen'); ?></a>

</div>

</form>
