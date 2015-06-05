<?php
/**
 * User: Hans-Gert Gräbe
 * Date: 05.06.2015
 * Get RDF out of an CSV in a rough way.
 * Returns RDF in Turtle notation, postprocess with any text editor. 
 */

//require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

function readCSV($filename) {
  if (($handle = fopen("$filename", "r")) !== FALSE) {
    $out=''; $row=1000;
    while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
      $out.=createRDF($row++,$data);
    }
    fclose($handle);
    return TurtlePrefix().$out;
  }
}

function createRDF($subject,$data) {
  $a=array();
  foreach ($data as $key => $value) {
    $fix=fixString($value);
    if (!empty($fix)) {
      if (strpos($fix,"http://") !== FALSE ) {
	$a[]="ld:predicate".$key." <$fix>"; 
      } else {
	$a[]="ld:predicate".$key." \"$fix\"";
      }
    }
  }
  return 
    '<http://leipzig-data.de/Data/CSVValue/'.$subject.">\n\t"
    .join(";\n\t",$a).".\n\n";
}

function fixString($string) {
  $string=trim($string);
  $string=str_replace("\"","\\\"",$string);
  return $string;
}

function TurtlePrefix() {
  return '
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix ical: <http://www.w3.org/2002/12/cal/ical#> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .
@prefix org: <http://www.w3.org/ns/org#> .
@prefix ld: <http://leipzig-data.de/Data/Model/> .
@prefix ldv: <http://leipzig-data.de/Data/CSVValue/> .


';
}


// Tests
echo readCSV("../Daten/akteure.csv"); 

?>


