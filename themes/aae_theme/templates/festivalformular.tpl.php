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

  $('#festivalOwner').change(function(e){
    if ($(this).find('option:selected').attr('value') == 'newAkteur'){
     $('#newAkteurAdresse').fadeIn('fast');
    } else {
     $('#newAkteurAdresse').fadeOut('fast');
    }
  });
  
 });
</script> 
<div class="row">
<h3>Festival <?php echo ($this->target == 'update' ? 'bearbeiten' : 'anlegen'); ?></h3>
<?php if ($this->target == 'update') : ?>
<a href="<?= base_path(); ?>akteurprofil/<?= $this->akteur_id; ?>/remove" class="small secondary button round right" style="display:none;margin-top:-37px;">Löschen</a>
<?php else : ?>
<div id="festival_switch" class="right switch large-1 columns" style="margin-top:-33px;display:none;">
 <input class="switch-input" id="barrierefrei" type="checkbox" name="barrierefrei"<?= ($this->barrierefrei || isset($_POST['barrierefrei']) ? ' checked="checked"' : ''); ?>>
 <label class="switch-paddle" for="barrierefrei" title="Barrierefreier Zugang?">
  <span class="show-for-sr">Barrierefreier Zugang?</span>
 </label>
</div>
<?php endif; ?>
<div class="divider" style="margin-bottom: 25px;"></div>

<?php if (!$this->freigabe) : ?>
<div class="callout alert">
 <p><?= t('Akteur konnte nicht gespeichert werden, da folgende Fehler vorliegen:'); ?></p><br />
  <?php foreach($this->fehler as $f) : ?>
    <p><strong><?= $f; ?></strong></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>
</div>

<form action="#" method="POST" enctype="multipart/form-data">

 <div class="row">
    <div class="large-4 columns">
      <?= $this->fehler['name']; ?>
      <label>Name <span class="pflichtfeld">(Pflichtfeld)</span>:
        <input type="text" id="akteurNameInput" name="name" value="<?= $this->name; ?>" placeholder="<?= t('Name des Festivals'); ?>" required />
      </label>
    </div>

    <div class="large-4 columns">
     <label>Email-Adresse <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['email']; ?>
      <input type="email" id="akteurEmailInput" name="email" value="<?= $this->email; ?>" placeholder="<?= t('E-mail Adresse'); ?>" required />
     </label>
    </div>    
    
   <div class="large-4 columns">
   <?php if ($this->target == 'update') : ?>
    <p>Festivalinhaber: <strong>NAME</strong></p>
   <?php else : ?>

   <label>Festivalinhaber <span class="pflichtfeld">(Pflichtfeld, kann nur EINAMLIG vergeben werden)</span>:
   <select id="festivalOwner" name="festivalOwner">
    <option value="newAkteur" selected="selected">+ Akteur anlegen</option>
    <?php foreach ($this->resultOwnAkteure as $akteur) : ?>
    <option value="<?= $akteur->AID; ?>"><?= $akteur->name; ?></option>
    <?php endforeach; ?>
   </select>
  
   <?php endif; ?>   
   </div>
   
   <div class="large-12 columns">
    <label>Festival-URL <span class="pflichtfeld">(Pflichtfeld, kann nur EINMALIG vergeben werden)</span>:
      <input type="url" id="akteurEmailInput" name="fUrl" value="<?= $this->url; ?>" placeholder="Steht hier 'kunstfest16', wird daraus https://leipziger-ecken.de/kunstfest16" required />
     </label>
    </div>

 </div><!-- /.row -->
 
 <?php if ($this->target != 'update') : ?>
  <fieldset id="newAkteurAdresse" class="Adresse fieldset row">
   <legend>Neuer Akteur: Basisinformationen & Name</legend>
   
   <div class="large-2 columns">
    <label>Akteurname:
     <input type="text" id="AkteurnameInput" name="akteurname" placeholder="Name des neuen Akteurs" />
    </label>
   </div>

   <div class="large-3 columns">
    <label>Straße: <?= $this->fehler['strasse']; ?>
     <input type="text" id="StrasseInput" name="strasse" value="<?= $this->strasse; ?>" placeholder="<?= t('Straße'); ?>" />
    </label>
   </div>

   <div class="large-1 columns">
    <label>Nr.: <?= $this->fehler['nr']; ?>
     <input type="text" id="NrInput" name="nr" value="<?= $this->nr; ?>" placeholder="<?= t('Hausnummer'); ?>" />
    </label>
   </div>

   <div class="large-3 columns">
    <label>Adresszusatz: <?= $this->fehler['adresszusatz']; ?>
     <input type="text" id="AdresszusatzInput" name="adresszusatz" value="<?= $this->adresszusatz; ?>" placeholder="<?= t('Adresszusatz'); ?>">
    </label>
   </div> 

   <div class="large-3 columns">
    <label>PLZ: <?= $this->fehler['plz']; ?>
      <input type="text" pattern="[0-9]{5}" id="PLZInput" name="plz" value="<?= $this->plz; ?>" placeholder="<?= t('PLZ'); ?>">
    </label>
   </div>
   <div class="large-4 columns">

  <label>Bezirk <span class="pflichtfeld">(Pflichtfeld)</span>: <?= $this->fehler['ort']; ?>

  <select name="ort">
   <option value="" selected="selected">Bezirk auswählen</option>
   <?php foreach ($this->resultBezirke as $bezirk) : ?>
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
   <input type="text" id="GPSInput" name="gps" value="<?= $this->gps; ?>" placeholder="<?= t('GPS-Adresskoordinaten'); ?>">
  </label>
  <p id="show_coordinates" style="display:none;"><a href="#" target="_blank"><?= t('Zeige Koordinaten auf Karte'); ?></a></p>
</div>

</fieldset>
<?php endif; ?>

<div class="row">

  <div class="large-12 columns">
  <label>Beschreibungstext für den Header (max. 1 Zeile)
   <textarea name="beschreibung" id="beschreibung" cols="45" rows="3" placeholder="Beschreibungstext des Festivals: Slogan, Datum, etc..."><?= $this->beschreibung; ?></textarea>
  </label>
  <script>CKEDITOR.replace('beschreibung');</script>
 </div>

 </div>

  <div class="row" id="akteurTabs">
  <div class="medium-3 columns">
    <ul class="tabs vertical" id="example-vert-tabs" data-tabs>
      <li class="tabs-title is-active"><a href="#pbild" aria-selected="true"><?= t('Festivalbild'); ?></a></li>
      <li class="tabs-title"><a href="#psponsoren">Sponsorenicons</a></li>
      <li class="tabs-title"><a href="#pakteure">Authorisierte Akteure</a></li>
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
      <p class="licensetext">Wir empfehlen, Bilder im <strong>Format 3:2</strong> hochzuladen (bspw. 640 x 400 Pixel)</p><br />

       </div>
       
       <div class="tabs-panel" id="psponsoren">
        <p class="licensetext">Link's' zu den Sponsoren-Icons. Die Skalierung in der Breite erfolgt automatisch</p>
        <input type="text" name="psponsoren[]" placholder="URL zum Icon" />
        <input type="text" name="psponsoren[]" placholder="URL zum Icon" />
        <p><a href="#">+ Weiteren Link einfügen</a></p>
      </div>
      
       <div class="tabs-panel" id="pakteure">
       
      </div>

    </div>
  </div>
</div>

 <div class="row" style="margin-top:15px;">
  <div class="large-12 columns">
  
  <label>Authorisierte Akteure</label>
  <select id="festivalAkteureInput" multiple="multiple" class="tokenize" name="festivalAkteure[]">
      
    <?php if (!empty($this->akteure)) : ?>
    <?php foreach ($this->festivalAkteure as $festivalAkteur) : ?>
     <option selected value="<?= $festivalAkteur->AID; ?>"><?= $festivalAkteur->name; ?></option>
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
   <p style="color:grey;">Festival eingetragen am <?= $this->created->format('d.m.Y, H:i'); ?> Uhr.
   <?php if ($this->modified->format('d.m.Y') != '01.01.1000' && $this->modified->format('H:i') != date('H:i')) : ?> Zuletzt aktualisiert am <?= $this->modified->format('d.m.Y, H:i'); ?> Uhr.<?php endif; ?>
   </p><div class="divider" style="margin:17px 0;"></div>
  <?php endif; ?>
 <?php endif; ?>

  <input type="submit" class="left button" id="akteurSubmit" name="submit" value="<?= t('Speichern'); ?>">
 </div>
</form>
