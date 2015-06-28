<?php 

/* Kurzübersicht über die Akteure erstellen */

require_once("lib/EasyRdf.php");

function getAkteursProfil($v) {
  $name=$v->get("rdfs:label");
  $url=$v->get("ki:hatAkteurVCard")->get("vcard:hasURL");
  $email=$v->get("ki:hatAkteurVCard")->get("vcard:hasEmail");
  $img=$v->get("ki:hatAkteurVCard")->get("vcard:hasPhoto");
  $telefon=$v->get("ki:hatAkteurVCard")->get("vcard:hasTelefon");
  $profile=$v->get("ki:hatAkteurProfil")->get("ki:hatKurzbeschreibung");
  $out='
<div style="float: left; width: 70%; ">
<h2>'.$name.'</h2>'; 
  if ($url) { $out.="<br/>URL: <a href=\"$url\">$url</a> " ; }
  If ($email) { $out.="<br/>Email: $email " ; } 
  if ($telefon) { $out.="<br/>Telefon: $telefon " ; } 
  if ($profile) { $out.="<br/>Profil: $profile " ; } 
  $out.='</div><div style="float: right; width: 30%">' ;
  if ($img) { 
    $out.='<img src="'.$img.'" width="100" style="float:right;" alt="'.$name.'" />';
  } 
  $out.='</div><div style="clear: both; margin-bottom: 30px; "></div>';
  return $out ; 
}


function htmlEnvelope($out) {
  return '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>
<body bgcolor="#ccffdd">'.$out.'
</body>
</html>
';
}

function listAkteure() { 
  EasyRdf_Namespace::set('vcard', 'http://www.w3.org/2006/vcard/ns#');
  EasyRdf_Namespace::set('ld', 'http://leipzig-data.de/Data/Model/');
  EasyRdf_Namespace::set('ki', 'http://kultur-initiative.net/Data/Model#');
  $graph = new EasyRdf_Graph("http://kultur-initiative.net/Data/Akteure/");
  $graph->parseFile("Akteure.json");
  $s=array();
  foreach ($graph->allOfType("ld:Akteur") as $v) {
    $s[]=getAkteursProfil($v); 
  } 
  return htmlEnvelope(implode("\n",$s));
}

echo listAkteure(); // for test: "php akteure.php >a.html"

?>
