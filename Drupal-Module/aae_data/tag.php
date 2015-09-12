<?php
	
	require_once $modulePath . '/database/db_connect.php';
	$db = new DB_CONNECT();
	global $user;

	//EID holen:
	$path = current_path();
	$explodedpath = explode("/", $path);
	$tag = $explodedpath[1];
	$tbl_event = "aae_data_event";//---r
	//DB-Abfrage aller Events, die an diesem Tag stattfinden---r
	$resultEvents = db_select($tbl_event, 'e')
	  ->fields('e', array( 
		'start',
		'name',
		'EID', 
	  ))
	  //->condition('start', db_like($this->currentDate.'%'), 'LIKE')
	  ->condition('start', $tag.'%', 'LIKE')
	  ->orderBy('name', 'ASC')
	  ->execute(); 
	foreach ($resultEvents as $row) {
		$events .= '<a href="?q=Eventprofil/'.$row->EID.'">'.$row->name.'</a><br>';	
		//$cellContent .= 'uiuiui';
	}
	

$profileHTML = <<<EOF
<p>$events</p>

EOF;
