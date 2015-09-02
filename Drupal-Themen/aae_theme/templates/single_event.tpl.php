<h3><?= $resultEvent->name; ?></h3>

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

<?php if($resultEvent->kurzbeschreibung != "") : ?>
  <h4>Beschreibung</h4>
  <p><?= $resultEvent->kurzbeschreibung; ?></p>
<?php endif; ?>

<?php if($resultEvent->url != "") : ?>
  <p>Url: <a href="<?= $resultEvent->url; ?>"><?= $resultEvent->url; ?></a></p>
<?php endif; ?>

<?php if($resultEvent->bild != "") : ?>
  <img src="<?= $resultEvent->bild; ?>">
<?php endif;

//Veranstalter

if(!empty($resultVeranstalter)) {

  foreach ($resultVeranstalter as $veranstalter) : ?>
    <h5>Veranstalter</h5>
    <a href="<?= base_path(); ?>Akteurprofil/<?= $veranstalter->AID; ?>"><?= $veranstalter->name; ?></a><br>
  <?php endforeach;
 }

	//Ersteller aus DB holen
	$ersteller = db_select("users", 'u')
	->fields('u', array(
	  'name',
	))
	->condition('uid', $resultEvent->ersteller, '=')
	->execute();
	foreach ($ersteller as $row2) : ?>
		<p>Erstellt von: <?= $row2->name; ?></p>
	<?php endforeach;

	//Adresse des Akteurs
	$resultAdresse = db_select($tbl_adresse, 'b')
	  ->fields('b', array(
	    'strasse',
	    'nr',
	    'plz',
	    'bezirk',
	    'gps',
	  ))
	  ->condition('ADID', $resultEvent->ort, '=')
	  ->execute();

	if($resultAdresse->rowCount() != 0){
	foreach ($resultAdresse as $row1) {

		if($row1->strasse != "" && $row1->nr != ""){
		  echo $row1->strasse.' '.$row1->nr.'<br>';
		}
		//Bezirksnamen holen:
		$resultBezirk = db_select($tbl_bezirke, 'z')
		  ->fields('z', array(
		    'bezirksname',
		  ))
		  ->condition('BID', $row1->bezirk, '=')
		  ->execute();

		foreach ($resultBezirk as $row2) {
		  if($row1->plz != ""){
		    $profileHTML .= $row1->plz.' ';
		  }
		  if($row2->bezirksname != ""){
		    $profileHTML .= $row2->bezirksname;
		  }
		  $profileHTML .= '<br>';
		}
		if($row1->gps != ""){
		  $profileHTML .= 'GPS: '.$row1->gps.'<br>';
		}
	}
	}

  if(count($sparten) != 0) { ?>
	  <p><strong>Tags:</strong>
	<?php  $laenge = count($sparten);
	  $j = 0;
	  while($j < $laenge){
	    echo $sparten[$j];
	    $j++;
	  }
    echo '</p>';
	} ?>

  <?= $profileHTML; ?>

<?php if($okay == 1) : ?>
  <a class="small secondary button" href="<?= base_path(); ?>Eventloeschen/<?= $eventId; ?>" >Event Löschen</a>
  <a class="small button" href="<?= base_path(); ?>Eventedit/<?= $eventId; ?>" >Event bearbeiten</a>
<?php endif; ?>
