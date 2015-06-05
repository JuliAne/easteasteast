<?php
/**
 * User: Hans-Gert Gräbe
 * Date: 05.06.2015
 * Get RDF out of an CSV - not yet finished
 */

//require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

function readCSV($filename) {
  if (($handle = fopen("$filename", "r")) !== FALSE) {
    $d=array();
    while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
      $d[$row++]=$data;
    }
    fclose($handle);
    return $d;
  }
}

function createRDF($data) {
  $a=array();
  $a["name"]=$data[0];
  $a["strasse"]=$data[1];
  return $a;
}


// Tests
$a=readCSV("../Daten/akteure.csv"); 
//print_r($a);
$out='';
foreach ($a as $i => $value) {
  $b=createRDF($value);
  print_r($b);
}

?>


