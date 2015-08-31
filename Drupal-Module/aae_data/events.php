<?php
/**
 * events.php listet alle Events auf.
 *
 * Ruth, 2015-07-10
 */

//-----------------------------------

$tbl_events = "aae_data_event";
$tbl_event_tags = "aae_data_event_hat_sparte";
$tbl_tags = "aae_data_kategorie";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();

$pathThisFile = $_SERVER['REQUEST_URI']; 

$resulttags = db_select($tbl_tags, 't')
  ->fields('t', array(
    'KID',
	'kategorie',
  ))
  ->execute();
$counttags = $resulttags->rowCount();

//-----------------------------------
if (isset($_POST['submit'])) {
  $tag = $_POST['tag'];
  if($tag != 0){
    //Auswahl der Events mit entsprechendem Tag in alphabetischer Reihenfolge
    $result = db_select($tbl_event_tags, 't');
    $result->join($tbl_events, 'e', 't.hat_EID = e.EID AND t.hat_KID = :kid', array(':kid' => $tag));
    $result->fields('e', array('name', 'EID', 'kurzbeschreibung', 'start'))->orderBy('name', 'ASC');
    $resultevents = $result->execute();
  }else{
	//Auswahl aller Events in alphabetischer Reihenfolge
    $resultevents = db_select($tbl_events, 'a')
    ->fields('a', array(	
      'name',
      'EID',
	  'kurzbeschreibung',
	  'start',
	))
    ->orderBy('name', 'ASC')	
    ->execute();
  }
}else{
  //Auswahl aller Events in alphabetischer Reihenfolge
  $resultevents = db_select($tbl_events, 'a')
    ->fields('a', array(
      'name',
      'EID',
	  'kurzbeschreibung',
	  'start',
    ))
    ->orderBy('start', 'ASC')
    ->execute();
}

//Ausgabe
$profileHTML = <<<EOF
EOF;

//Abfrage, ob Besucher der Seite eingeloggt ist:
if(user_is_logged_in()){//Link für Generierung eines neuen Akteurs anzeigen
  $profileHTML .= '<a href="?q=Eventformular">Neue Veranstaltung hinzufügen!</a><br><br>';
}

$profileHTML .= <<<EOF
<form action='$pathThisFile' method='POST' enctype='multipart/form-data'>
EOF;
//Auswahl eines Tags
$profileHTML .= '<select name="tag" size="'.$counttags.'" >';
$profileHTML .= '<option value="0" selected="selected" >keine Auswahl</option>';
foreach ($resulttags as $row) {
  $profileHTML .= '<option value="'.$row->KID.'">'.$row->kategorie.'</option>';
}
$profileHTML .= '</select>';
$profileHTML .= '<input type="submit" class="event" id="eventSubmit" name="submit" value="OK">';

$profileHTML .= <<<EOF
</form>
EOF;

//Ausgabe der Events
foreach($resultevents as $row){
  $profileHTML .= '<p>'.$row->start.' <a href="?q=Eventprofil/'.$row->EID.'">'.$row->name.'</a>: '.$row->kurzbeschreibung.'</p><br>';
}
