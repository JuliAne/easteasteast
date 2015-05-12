<?php 

/* Beispiel zur Einbindung von Daten aus einem entfernten RDF-Store über eine
   SPARQL Query

*/

require_once("lib/EasyRdf.php");

function get_store() { return 'http://symbolicdata.org:8891/sparql'; }

function addPerson($v) {
  $a=$v->get('rdfs:seeAlso');
  $anker=str_replace("http://symbolicdata.org/Data/Person/", "", $a);
  $name=$v->get('foaf:title')." "
    .$v->get('foaf:givenName')." "
    .$v->get('foaf:familyName');
  $url=$v->get('foaf:homepage');
  $img=$v->get('foaf:image');
  $email=$v->get('foaf:mbox');
  $telefon=$v->get('foaf:phone');
  $out='
<div style="float: left; width: 70%; ">
<strong><a name="'.$anker.'" href="'.$a.'">'.$name.'</a></strong>'; 
  if ($url) { $out.="<br/>URL: <a href=\"$url\">$url</a> " ; }
  If ($email) { $out.="<br/>Email: $email " ; } 
  if ($telefon) { $out.="<br/>Telefon: $telefon " ; } 
  $out.='</div><div style="float: right; width: 30%">' ;
  if ($img) { 
    $out.='<img src="'.$img.'" width="100" style="float:right;" alt="'.$name.'" />';
  } 
  $out.='</div><div style="clear: both; margin-bottom: 30px; "></div>';
  return $out ; 
}

function fgl($atts) {
  $query = '
PREFIX sd: <http://symbolicdata.org/Data/Model#>
construct { ?p ?p1 ?p2 . }
from <http://symbolicdata.org/casn/FOAF-Profiles/>
from <http://symbolicdata.org/casn/Groups/>
where {
  ?p a foaf:Person ; ?p1 ?p2 .
  ?p rdfs:seeAlso ?q .
  <http://symbolicdata.org/casn/Group/FGL2011> foaf:member ?q .
} 
';
  
  $sparql = new EasyRdf_Sparql_Client(get_store());
  $result=$sparql->query($query); // a CONSTRUCT query returns an EasyRdf_Graph
  $h=array(); // prepare result for sorting
  foreach ($result->resources() as $v) {
    $name=$v->get('foaf:familyName');
    $h["$name"]=addPerson($v);
  }
  ksort($h); // sort the entries by familyName 
  $out=''; // compose the output 
  foreach ($h as $k => $v) { $out.=$v; }
  return $out;
}

function testFGL() { 
  $a=array(); 
  echo '<meta charset="utf8">'.fgl($a);
}

testFGL(); // for test 

?>
