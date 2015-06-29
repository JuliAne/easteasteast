<?php

class DB_CONNECT {

  //constructor
  function __construct() {
    //connecting to DB
    $this->connect();
  }
	
  //destructor
  function __destruct() {
    //closing connection
    $this->close();
  }
	
  function connect() {
    //import db_config
    require_once __DIR__ . '/db_config.php';
		
    //$connection = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());
		
    //$db = mysql_select_db(DB_DATABASE) or die(mysql_error());
    $host = DB_SERVER;
    $db = DB_DATABASE;
    $connection = new PDO("mysql:host=$host;dbname=$db", DB_USER, DB_PASSWORD) or die("<p>Fehler!</p></body></html>");
		
    return $connection;
  }
	
  function close() {
	
    //mysql_close();
	
  }

}

?>