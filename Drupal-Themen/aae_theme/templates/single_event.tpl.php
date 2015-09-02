<h3 class="left"><?= $resultEvent->name; ?></h3>

<p class="right">
<strong>Zeit:</strong>

<?php if($resultEvent->start != "") {
  $explodedstart = explode(' ', $resultEvent->start);
  $explodedende = explode(' ', $resultEvent->ende);

  echo $explodedstart[0];

  if($resultEvent->ende != $explodedstart[0]){
   echo '- '.$explodedende[0];
  }

  if($explodedstart[1] != "" && $explodedende[1] != ""){
   echo '<br>'.$explodedstart[1].'-'.$explodedende[1].'<br>';
  }
} ?>
</p>

<div class="divider" style="padding: 20px 0;"></div>

<?php if($resultEvent->kurzbeschreibung != "") : ?>
  <h4 style="padding: 10px 0;">Beschreibung</h4>
  <p><?= $resultEvent->kurzbeschreibung; ?></p>
<?php endif;

if(count($sparten) != 0) { ?>
  <br /><p><strong>Tags:</strong>
<?php  $laenge = count($sparten);
  $j = 0;
  while($j < $laenge){
    echo $sparten[$j].' ';
    $j++;
  }
  echo '</p>';
} ?>

<?php if($resultEvent->bild != "") : ?>
  <img style="padding: 10px 0;" src="<?= $resultEvent->bild; ?>">
<?php endif; ?>

 <h4 style="padding: 10px 0;">Veranstalter</h4>

 <?php foreach ($ersteller as $row2) : ?>
   <p><strong>Erstellt von:</strong> <?= $row2->name; ?></p>
 <?php endforeach; ?>

   <p>Akteur: <a href="<?= base_path(); ?>Akteurprofil/<?= $resultVeranstalter->AID; ?>"><?= $resultVeranstalter->name; ?></a></p>

   <?php if(!empty($resultAdresse)) : ?>
   <p><strong>Ort:</strong>

   <?php if($resultAdresse->strasse != "" && $resultAdresse->nr != "") : ?>
       <?= $resultAdresse->strasse.' '.$resultAdresse->nr; ?>
   <?php endif; ?>

  <?php if($resultAdresse->plz != "") : ?>
      <?= $resultAdresse->plz; ?>
  <?php endif; ?>

  <?php  if($resultBezirk->bezirksname != "") : ?>
      <?= $resultBezirk->bezirksname; ?>
  <?php endif; ?>

  <?php if($resultAdresse->gps != "") : ?>
       GPS (MAP HIER): <?= $resultAdresse->gps; ?>
  <?php endif; ?>

  </p><?php endif; ?>

  <?php if($resultEvent->url != "") : ?>
    <br /><p><strong>Weitere Informationen: </strong><a href="<?= $resultEvent->url; ?>"><?= $resultEvent->url; ?></a></p>
  <?php endif; ?>

<?php if($okay == 1) : ?>
  <a class="right small secondary button" href="<?= base_path(); ?>Eventloeschen/<?= $eventId; ?>" >Event Löschen</a>
  <a class="right small button" href="<?= base_path(); ?>Eventedit/<?= $eventId; ?>" >Event bearbeiten</a>
<?php endif; ?>
