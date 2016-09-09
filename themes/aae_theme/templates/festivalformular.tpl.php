<script type="text/javascript">
 $(document).ready(function(){
  $('#festivalAkteureInput').tokenize({
    displayDropdownOnFocus : true,
    newElements : false,
    onRemoveToken : function(value, e){

     if((parseFloat(value) == parseInt(value)) && !isNaN(value)) {
      $('form').append('<input type="hidden" name="removedAkteur[]" value="'+value+'" />');
     }

    }
  });

  $('#festivalAkteur').change(function(e){
    if ($(this).find('option:selected').attr('value') == 'newAkteur'){
     $('#newAkteurAdresse').fadeIn('fast');
    } else {
     $('#newAkteurAdresse').fadeOut('fast');
    }
  });
  
 });
</script> 
<style type="text/css">
 #cke_1_contents { height:60px !important; }
 #akteur_tabs { margin-top:20px; }
</style>
<div class="row">
<h3><?= t('Festival'); ?> <?= ($this->target == 'update' ? t('bearbeiten') : t('anlegen')); ?></h3>
<?php if ($this->target == 'update') : ?><a href="<?= base_path(); ?>festival/remove" class="small secondary button round right" style="display:none;margin-top:-37px;" title="Festival löschen (TODO)"><?= t('Löschen'); ?></a><?php endif; ?>
<div class="divider" style="margin-bottom: 25px;"></div>

<?php if (!$this->freigabe) : ?>
<div class="callout alert">
 <p><?= t('Festival konnte nicht gespeichert werden, da folgende Fehler vorliegen:'); ?></p><br />
 <?php foreach ($this->fehler as $f) : ?>
  <p><strong><?= $f; ?></strong></p>
 <?php endforeach; ?>
</div>
<?php endif; ?>
</div>

<form action="#" method="POST" enctype="multipart/form-data">

 <div class="row">
    <div class="large-4 columns">
      <?= $this->fehler['name']; ?>
      <label><?= t('Name'); ?> <span class="pflichtfeld">(Pflichtfeld)</span>:
        <input type="text" id="akteurNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= t('Name des Festivals'); ?>" required />
      </label>
    </div>

    <div class="large-4 columns">
     <label><?= t('Email-Adresse'); ?> <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['email']; ?>
      <input type="email" id="akteurEmailInput" name="email" value="<?= $this->email; ?>" placeholder="<?= t('E-mail Adresse'); ?>" required />
     </label>
    </div>    
    
   <div class="large-4 columns">
   <?php if ($this->target == 'update') : ?>
    <p><?= t('Festivalinhaber'); ?>: <strong><?= $this->festivalAkteur->name; ?></strong></p>
   <?php else : ?>
   <label><?= t('Festivalinhaber'); ?> <span class="pflichtfeld">(Pflichtfeld, kann nur EINMALIG vergeben werden)</span>:
   <select id="festivalAkteur" name="festivalAkteur">
    <option value="newAkteur"<?= (empty($_POST['festivalAkteur']) ? 'selected="selected"' : ''); ?>>+ <?= t('Akteur anlegen'); ?></option>
    <?php foreach ($this->ownedAkteure as $akteur) : ?>
    <option value="<?= $akteur->AID; ?>"<?= ($akteur->AID == $_POST['festivalAkteur'] ? 'selected="selected"' : ''); ?>><?= $akteur->name; ?></option>
    <?php endforeach; ?>
   </select>
  
   <?php endif; ?>   
   </div>
   
   <div class="large-12 columns">
    <label>Festival-URL <span class="pflichtfeld">(Pflichtfeld, kann nur EINMALIG vergeben werden)</span>:
      <input type="text" id="akteurEmailInput" name="alias" value="<?= $this->alias; ?>" placeholder="Steht hier 'kunstfest16', wird daraus https://leipziger-ecken.de/kunstfest16" required<?= ($this->target == 'update' ? ' disabled' : ''); ?> />
     </label>
    </div>

 </div><!-- /.row -->
 
 <?php if ($this->target != 'update') : ?>
  <fieldset id="newAkteurAdresse" class="Adresse fieldset row">
   <legend>Neuer Akteur: Basis-informationen</legend>
   <p>Hinweis: Jedes Festival benötigt einen Akteur, dessen Profil mit dem Festival verknüpft wird und als Info-Seite dient.</p>
   <p>Sollte bereits ein zuständiger Akteur bestehen, wählen Sie diesen bitte oben unter "Veranstalter" aus!</p>
   
   <div class="large-2 columns">
    <label><?= t('Name') ?>: <?= $this->fehler['akName']; ?>
     <input type="text" id="AkteurnameInput" name="akName" placeholder="Name des neuen Akteurs" value="<?= $this->akName; ?>" />
    </label>
   </div>

   <div class="large-3 columns">
    <label><?= t('Straße'); ?>: <?= $this->fehler['akStrasse']; ?>
     <input type="text" id="StrasseInput" name="akStrasse" value="<?= $this->akStrasse; ?>" placeholder="<?= t('Straße'); ?>" />
    </label>
   </div>

   <div class="large-1 columns">
    <label><?= t('Hausnummer'); ?>: <?= $this->fehler['akNr']; ?>
     <input type="text" id="NrInput" name="akNr" value="<?= $this->akNr; ?>" placeholder="<?= t('Hausnummer'); ?>" />
    </label>
   </div>

   <div class="large-3 columns">
    <label><?= t('Adresszusatz'); ?>: <?= $this->fehler['akAdresszusatz']; ?>
     <input type="text" id="AdresszusatzInput" name="akAdresszusatz" value="<?= $this->akAdresszusatz; ?>" placeholder="<?= t('Adresszusatz'); ?>">
    </label>
   </div> 

   <div class="large-3 columns">
    <label><?= t('PLZ'); ?>: <?= $this->fehler['akPlz']; ?>
      <input type="text" pattern="[0-9]{5}" id="PLZInput" name="akPlz" value="<?= $this->akPlz; ?>" placeholder="<?= t('PLZ'); ?>">
    </label>
   </div>
   <div class="large-4 columns">

  <label><?= t('Bezirk'); ?> <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['ort']; ?>

  <select name="akOrt">
   <option value="" selected="selected">- <?= t('Bezirk auswählen'); ?> -</option>
   <?php foreach ($this->resultBezirke as $bezirk) : ?>
    <option value="<?= $bezirk->BID; ?>"<?= ($bezirk->BID == $this->akOrt ? ' selected="selected"' : ''); ?>><?= $bezirk->bezirksname; ?></option>
   <?php endforeach; ?>
  </select>
  </label>
 </div>

  <div class="large-4 columns">
  <label><?= t('Geodaten (Karte)'); ?>: <?= $this->fehler['akGps']; ?>
   <input type="text" id="GPSInput" name="akGps" value="<?= $this->akGps; ?>" placeholder="<?= t('GPS-Adresskoordinaten'); ?>">
  </label>
  <p id="show_coordinates" style="display:none;"><a href="#" target="_blank"><?= t('Zeige Koordinaten auf Karte'); ?></a></p>
</div>

</fieldset>
<?php endif; ?>

<div class="row">

  <div class="large-12 columns">
  <label>Beschreibungstext für den Header (max. 1 Zeile)
   <textarea name="beschreibung" id="beschreibung" cols="45" rows="1" placeholder="Beschreibungstext des Festivals: Slogan, Datum, etc..."><?= $this->beschreibung; ?></textarea>
  </label>
  <script>CKEDITOR.replace('beschreibung');</script>
 </div>

 </div>

  <div class="row" id="akteurTabs">
  <div class="medium-3 columns">
    <ul class="tabs vertical" id="example-vert-tabs" data-tabs>
     <li class="tabs-title is-active"><a href="#pbild" aria-selected="true"><?= t('Festivalbild'); ?></a></li>
     <li class="tabs-title"><a href="#psponsoren"><?= t('Sponsoren'); ?></a></li>
    </ul>
    </div>
    <div class="medium-9 columns">
    <div class="tabs-content vertical" data-tabs-content="example-vert-tabs">
     <div class="tabs-panel is-active" id="pbild">
      <label for="akteurBildInput" class="button"><?= t('Hintergrundbild für Festival hochladen'); ?>...</label>
      <input type="file" id="akteurBildInput" name="bild" class="show-for-sr" />

        <?php if (!empty($this->bild)) : ?>
          <input type="hidden" name="oldPic" value="<?= $this->bild; ?>" />
          <div id="currentPic">
           <img src="<?= $this->bild; ?>" title="<?= t('Aktuelles Festivalbild'); ?>" />
          </div>
        <?php endif; ?>
        <p class="licensetext">TODO: Wir empfehlen, Bilder im <strong>Format 3:2</strong> hochzuladen (bspw. 640 x 400 Pixel)</p><br />

       </div>
       
       <div class="tabs-panel" id="psponsoren">
        <p class="licensetext">Link's zu den Sponsoren-Icons. Die Skalierung in der Breite erfolgt automatisch.</p>
        <input type="text" name="sponsoren[]" placholder="URL zum Icon" />
        <input type="text" name="sponsoren[]" placholder="URL zum Icon" />
        <p><a href="#">+ Weiteren Link einfügen</a></p>
      </div>

    </div>
  </div>
</div>

 <div class="row" style="margin-top:15px;">
  <div class="large-12 columns">
  
  <label><?= t('Authorisierte Akteure'); ?></label>
  <select id="festivalAkteureInput" multiple="multiple" class="tokenize" name="authorizedAkteure[]">
      
    <?php if (!empty($this->authorizedAkteure)) : ?>
    <?php foreach ($this->authorizedAkteure as $akteur) : ?>
     <option selected value="<?= $akteur->AID; ?>"><?= $akteur->name; ?></option>
    <?php endforeach;?>
    <?php endif; ?>

    <?php foreach ($this->allAkteure as $akteur) : ?>
     <option value="<?= $akteur->AID; ?>"><?= $akteur->name; ?></option>
    <?php endforeach;?>
    </select>
  </div>
 </div>
 
 <div class="row">
 <?php if ($this->target == 'update' && !empty($this->created)) : ?>
  <?php if ($this->created->format('d.m.Y') != '01.01.1000') : ?>
   <p style="color:grey;"><?= t('Festival'); ?> <?= t('eingetragen am'); ?> <?= $this->created->format('d.m.Y, H:i'); ?> <?= t('Uhr'); ?>.
   <?php if ($this->modified->format('d.m.Y') != '01.01.1000' && $this->modified->format('H:i') != date('H:i')) : ?> <?= t('Zuletzt aktualisiert am'); ?> <?= $this->modified->format('d.m.Y, H:i'); ?> <?= t('Uhr'); ?>.<?php endif; ?>
   </p><div class="divider" style="margin:17px 0;"></div>
  <?php endif; ?>
 <?php endif; ?>

  <input type="submit" class="left button" id="akteurSubmit" name="submit" value="<?= t('Speichern'); ?>">
 </div>
</form>
