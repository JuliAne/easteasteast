<?php if (!empty($_SESSION['sysmsg'])) : ?>
<div id="alert">
  <?php foreach ($_SESSION['sysmsg'] as $msg): ?>
    <?= $msg; ?>
  <?php endforeach; ?>
  <a href="#" class="close">x</a>
</div>
<?php unset($_SESSION['sysmsg']); endif; ?>

<?php if (!empty($resultAdresse->gps)) : ?>
<div id="map" style="height:280px;width:100%;margin-bottom:20px;"></div>
<?php endif; ?>

<h3 class="left"><?= $resultEvent->name; ?></h3>

<p class="right">
<strong>Start: </strong><?= $resultEvent->start; ?> (<?= $resultEvent->zeit_von; ?>)
<?php if (!empty($resultEvent->ende)) echo ' - <strong>Ende:</strong> '.$resultEvent->ende;
      if (!empty($resultEvent->zeit_bis)) echo ' ('.$resultEvent->zeit_bis.')'; ?>
</p>

<div class="divider" style="padding: 20px 0;"></div>

<?php if(!empty($resultEvent->kurzbeschreibung)) : ?>
  <h4 style="padding: 10px 0;">Beschreibung</h4>
  <p><?= $resultEvent->kurzbeschreibung; ?></p>
<?php endif;

if(count($sparten) != 0) { ?>
  <br /><p><strong>Tags: </strong>
<?php $laenge = count($sparten);
  $j = 0;
  while($j < $laenge){
    echo $sparten[$j].' ';
    $j++;
  }
  echo '</p>';
} ?>

<?php if($resultEvent->bild != "") : ?>
  <img style="padding: 10px 0;" src="<?= $resultEvent->bild; ?>" title="<?= $resultEvent->name; ?>">
<?php endif; ?>

 <h4 style="padding: 10px 0;">Veranstalter</h4>

 <?php foreach ($ersteller as $row2) : ?>
   <p><strong>Erstellt von:</strong> <?= $row2->name; ?></p>
 <?php endforeach; ?>

   <p><strong>Akteur:</strong> <a href="<?= base_path(); ?>Akteurprofil/<?= $resultAkteur['AID']; ?>" title="Profil von <?= $resultAkteur['name']; ?> besuchen"><?= $resultAkteur['name']; ?></a></p>

   <?php if(!empty($resultAdresse)) : ?>
    <p><strong>Ort:</strong>

    <?php if($resultAdresse->strasse != "" && $resultAdresse->nr != "") : ?>
       <?= $resultAdresse->strasse.' '.$resultAdresse->nr; ?>
    <?php endif; ?>

   <?php if($resultAdresse->plz != "") : ?>
      - <?= $resultAdresse->plz; ?> Leipzig
   <?php endif; ?>

   <?php  if($resultBezirk->bezirksname != "") : ?>
      (<?= $resultBezirk->bezirksname; ?>)
   <?php endif; ?>

   <?php //if($resultAdresse->gps != "") : ?>

  </p><?php endif; ?>

  <?php if($resultEvent->url != "") : ?>
    <br /><p><strong>Weitere Informationen: </strong><a href="<?= $resultEvent->url; ?>"><?= $resultEvent->url; ?></a></p>
  <?php endif; ?>

  <form action="/Tag/<?= $resultEvent->start; ?>" method="POST">
    <input name="eventid" value="16" type="hidden">
    <input class="right small secondary button" id="icalSubmit" name="submit" value="Als .ical exportieren" type="submit">
  </form>

<?php if($okay == 1) : ?>
  <a class="right small secondary button" href="<?= base_path(); ?>Eventloeschen/<?= $eventId; ?>" >Event Löschen</a>
  <a class="right small button" href="<?= base_path(); ?>Eventedit/<?= $eventId; ?>" >Event bearbeiten</a>
<?php endif; ?>
