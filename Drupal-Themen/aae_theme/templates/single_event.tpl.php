<h3><?= $resultEvent->name; ?></h3>

<?= print_r($resultEvent); ?>

<?php if($resultEvent->kurzbeschreibung != "") : ?>
  <h4>Beschreibung</h4>
  <p><?= $resultEvent->kurzbeschreibung; ?></p>
<?php endif; ?>

<?php if($resultEvent->bild != "") : ?>
  <img src="<?= $resultEvent->bild; ?>">
<?php endif; ?>

	<?php //Veranstalter
	if($resultVeranstalter->rowCount() != 0){

	foreach ($resultVeranstalter as $row1) {
	  $resultAkteur = db_select($tbl_akteur, 'b')
	  ->fields('b', array(
	    'name',
	  ))
	  ->condition('AID', $row1->AID, '=')
	  ->execute();

	  foreach ($resultAkteur as $row2) : ?>
      <h5>Veranstalter</h5>
	  	<a href="?q=Akteurprofil/<?= $row1->AID; ?>"><?= $row2->name; ?></a><br>
    <?php endforeach;
	}
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
	$resultadresse = db_select($tbl_adresse, 'b')
	  ->fields('b', array(
	    'strasse',
	    'nr',
	    'plz',
	    'bezirk',
	    'gps',
	  ))
	  ->condition('ADID', $resultEvent->ort, '=')
	  ->execute();

	if($resultadresse->rowCount() != 0){
	foreach ($resultadresse as $row1) {

		if($row1->strasse != "" && $row1->nr != ""){
		  echo $row1->strasse.' '.$row1->nr.'<br>';
		}
		//Bezirksnamen holen:
		$resultbezirk = db_select($tbl_bezirke, 'z')
		  ->fields('z', array(
		    'bezirksname',
		  ))
		  ->condition('BID', $row1->bezirk, '=')
		  ->execute();
		foreach ($resultbezirk as $row2) {
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

	//Datum
	$profileHTML .= '<h4>Zeit:</h4>';
	if($resultEvent->start != "") {
	  $explodedstart = explode(' ', $row->start);
	  $explodedende = explode(' ', $row->ende);
	  $profileHTML .= $explodedstart[0];
	  if($row->ende != $explodedstart[0]){
		$profileHTML .= '- '.$explodedende[0];
	  }
	  if($explodedstart[1] != "" && $explodedende[1] != ""){
	    $profileHTML .= '<br>'.$explodedstart[1].'-'.$explodedende[1].'<br>';
	  }
	}
	if($row->url != "") { $profileHTML .= '<br><a href="'.$row->url.'">'.$row->url.'</a><br>'; }

	//Sparten:
    if(count($sparten) != 0){
	  $profileHTML .= '<br>Tags:<br>';
	  $laenge = count($sparten);
	  $j = 0;
	  while($j < $laenge){
	    $profileHTML .= '<p>'.$sparten[$j].'</p>';
	    $j = $j+1;
	  }
	} ?>

  <?= $profileHTML; ?>

<?php if($okay == 1) : ?>
  <a class="small secondary button" href="?q=Eventloeschen/<?= $eventId; ?>" >Event Löschen</a>
  <a class="small button" href="?q=Eventedit/<?= $eventId; ?>" >Event bearbeiten</a>
<?php endif; ?>
