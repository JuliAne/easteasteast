<?php
/**
 * User: Hans-Gert Gräbe
 * Date: 11.06.2015
 * 
 */

require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

function runQuery() {
  $query = '
select distinct ?b
from <http://kultur-initiative.net/Data/Akteure/>
where {
  ?a <http://www.w3.org/2006/vcard/ns#hasPhoto> ?b .
} 
';
  
  $sparql = new EasyRdf_Sparql_Client('http://localhost:8890/sparql');
  $result=$sparql->query($query); // a SELECT query returns an object of type EasyRdfSparqlResult.
  EasyRdf_Namespace::set('vcard', 'http://www.w3.org/2006/vcard/ns#');
  foreach ($result as $v) {
    getFile($v->b);
  }
}

function getFile($fn) {
  // Lege vorher lokal das Verzeichnis images an.
  if (!copy("http://kultur-initiative.net/$fn", $fn)) {
    echo "copy $fn schlug fehl...\n";
  }
}

runQuery();

?>


