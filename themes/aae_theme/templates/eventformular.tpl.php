<?php $recurringEventTypes = array(
   '2' => t('Wöchentliche Wiederholung'),
   '3' => t('2-wöchentliche Wiederholung'),
   '4' => t('Monatliche Wiederholung'),
   '5' => t('2-monatliche Wiederholung')
  ); ?>
<div class="row">
 <h3><?= t('Event'); ?> <?= ($this->target == 'update' ? t('bearbeiten') : t('anlegen')); ?></h3>
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
  <option value="0"<?= ($this->target == 'update' && empty($akteur->AID) && empty($this->FID) ? ' class="isPrivate" selected="selected"' : ''); ?>><?= t('Privat'); ?></option>
  <?php if ($this->ownedFestivals && is_array($this->ownedFestivals)) : ?>
   <optgroup label="<?= t('Festivals'); ?>">
   <?php foreach ($this->ownedFestivals as $festival) : ?>
    <option class="isFestival" value="f<?= $festival['FID']; ?>"<?= ($festival['FID'] == $this->FID ? ' selected="selected"' : ''); ?>><?= $festival['name']; ?></option>
   <?php endforeach; ?>
   </optgroup>
  <?php endif; ?>
  <?php if (is_array($this->resultAkteure) && !empty($this->resultAkteure)) : ?>
   <optgroup label="<?= t('Akteure'); ?>">
   <?php foreach ($this->resultAkteure as $akteur) : ?>
    <option value="<?= $akteur->AID; ?>"<?= (($akteur->AID == $this->akteur_id && empty($this->FID)) ? 'selected="selected"' : '') ?>><?= $akteur->name; ?></option>
   <?php endforeach; ?>
   </optgroup>
  <?php endif; ?>
  </select>
  </div>

  <div class="row">
   <fieldset class="fieldset">
   <legend><?= t('Datum'); ?></legend>

   <div class="large-3 columns">
    <label>Start (Datum, <span class="pflichtfeld"><?= t('Pflichtfeld'); ?></span>): <?= $this->fehler['start']; ?>
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

   <div id="recurrSwitch" class="switch large-2 columns" style="text-align:right;<?= (!empty($this->FID) ? 'display:none;' : ''); ?>">
    <input class="switch-input" id="eventRecurres" type="checkbox" name="eventRecurres"<?= (empty($this->FID) && ($this->eventRecurres || isset($_POST['eventRecurres'])) ? ' checked="checked"' : ''); ?>>
    <label class="switch-paddle" for="eventRecurres" title="<?= t('Sich wiederholendes Event?'); ?>">
    <span class="show-for-sr"><?= t('Sich wiederholendes Event?'); ?></span>
   </div>

   <div id="eventRecurresData" class="large-12 columns"<?= (empty($this->FID) && (!empty($this->recurringEventType) || isset($_POST['eventRecurres'])) ? '' : ' style="display:none;"'); ?>>

    <p class="large-12 columns licensetext"><strong>Wiederkehrende Veranstaltung.</strong> Beta-Feature; Setzt automatisch bis zu fünf Termine in einem abfolgenden Events-Rhytmus. Einzelne Termine können in der Terminliste auf der Eventseite entfernt werden.</p>

    <div class="large-4 left columns">
     <label><?= t('Rhytmus:'); ?> <?= $this->fehler['eventRecurres']; ?>

     <select id="eventRecurringType" name="eventRecurringType">
     <?php foreach ($recurringEventTypes as $key => $value) : ?>
      <option value="<?= $key; ?>"<?= ($key == $this->recurringEventType || $key == $_POST['eventRecurringType'] ? ' selected="selected"' : '') ?>><?= $value; ?></option>
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
   <legend><?= t('Adresse'); ?></legend>

   <div class="large-4 columns">
    <label><?= t('Straße'); ?>: <?= $this->fehler['strasse']; ?>
     <input type="text" id="StrasseInput" name="adresse[strasse]" value="<?= $this->adresse->strasse; ?>" placeholder="<?= t("Straße"); ?>">
    </label>
   </div>

   <div class="large-2 columns">
    <label><?= t('Hausnummer'); ?>: <?= $this->fehler['nr']; ?>
     <input type="text" id="NrInput" name="adresse[nr]" value="<?= $this->adresse->nr; ?>" placeholder="<?= t('Nr.'); ?>">
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

   <div class="large-4 columns">
   <label><?= t('Bezirk'); ?>: <?= $this->fehler['bezirk']; ?>

   <select name="adresse[bezirk]">
    <option value="" selected="selected">- <?= t('Bezirk auswählen'); ?> -</option>
    <?php foreach ($this->resultBezirke as $bezirk) : ?>
    <option value="<?= $bezirk->BID; ?>"<?= ($bezirk->BID == $this->adresse->bezirk ? ' selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
    <?php endforeach; ?>
   </select>
   </label>
  </div>

  <div class="large-4 columns">
  <label><?= t('Geodaten (Karte)'); ?>: <?= $this->fehler['gps']; ?>
   <input type="text" id="GPSInput" name="adresse[gps]" value="<?= $this->adresse->gps; ?>" placeholder="<?= t('GPS Koordinaten'); ?>">
  </label>
  <p id="show_coordinates" style="display:none;"><a href="#" target="_blank"><?= t('Zeige Koordinaten auf Karte'); ?></a></p>
</div>

</fieldset></div>

 <div class="row">

  <div class="large-12 columns">
  <label><?= t('Beschreibung. In der Vorschau erscheinen die ersten 30 Wörter'); ?> <span class="pflichtfeld">(<?= t('Pflichtfeld'); ?>)</span>: <?= $this->fehler['kurzbeschreibung']; ?>
   <textarea name="kurzbeschreibung" id="kurzbeschreibung" cols="45" rows="3" placeholder="<?= t("Beschreibungstext"); ?>"><?= $this->kurzbeschreibung; ?></textarea>
  </label>
  <script>CKEDITOR.replace('kurzbeschreibung', { toolbar : 'Basic' });</script>
 </div>

 </div>

 <div class="row">
  <fieldset class="fieldset">
   <legend><?= t('Eventbild'); ?></legend>

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
    <p class="licensetext">Wir veröffentlichen alle Inhalte unter der Free cultural Licence <i>„CC-By 4.0 international“</i> - Dies bedeutet jeder darf ihre Daten nutzen und bearbeiten wenn er den Urheber nennt. Wir bitten Sie, Ihre Daten nach besten Wissen und Gewissen über die Eingabefelder zu beschreiben.</p><br />
    <p class="licensetext">Wir übernehmen keinerlei Haftung für Schadensersatzforderung (o.ä.) in Bezug auf Dritte.</p>
    <p class="licensetext">Bildmaterial sollte abgeklärt werden mit erkennbaren Menschen. Haftung übernimmt der Urheber.</p>
   </fieldset>
  </div>

  <div class="row">
   <div class="large-12 columns">

    <label><?= t('Kategorien:'); ?> <?= $this->fehler['tags']; ?></label>

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

 <div class="row">
 <?php if ($this->target == 'update' && !empty($resultEvent->created)) : ?>
  <?php if ($this->created->format('d.m.Y') != '01.01.1000') : ?>
  <p style="color:grey;"><?= t('Event eingetragen am'); ?> <?= $this->created->format('d.m.Y, H:i'); ?> <?= t('Uhr'); ?>.
  <?php if ($this->modified->format('d.m.Y') != '01.01.1000' && $this->modified->format('H:i') != date('H:i')) : ?> <?= t('Zuletzt aktualisiert am'); ?> <?= $this->modified->format('d.m.Y, H:i'); ?> <?= t('Uhr'); ?>.<?php endif; ?>
  </p><div class="divider" style="margin:17px 0;"></div>
  <?php endif; ?>
 <?php endif; ?>
 
  <div id="festivalSubmitText" style="display:none;"><?= t('Festivalevent speichern und weiter...'); ?></div>
  <div id="submitText" style="display:none;"><?= t('Speichern'); ?></div>
  <input type="submit" class="left button" id="eventSubmit" name="submit" value="<?= t('Speichern'); ?>"> 
  <a class="secondary right button" href="<?= base_path(); ?>events" title="<?= t('Zurück zu den Events'); ?>"><?= t('Abbrechen'); ?></a>

</div>

</form>
