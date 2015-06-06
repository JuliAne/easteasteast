<?php
/**
 * User: Hans-Gert Gräbe
 * Datum: 06.06.2015

 * Generisches Werkzeug, um RDF aus einer CSV-Datei zu erzeugen.

 * Gibt RDF in Turtlenotation zurück zur Nachbearbeitung mit einem
 * Textprozessor der eigenen Wahl.

 */

//require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

/* Generischer Treiber, dem eine Funktion als Parameter übergeben wird, mit der
   ein einzelner Datensatz der CSV-Datei verarbeitet wird. */

function readCSV($filename,$ontologyPrefix,$processing) {
  if (($handle = fopen("$filename", "r")) !== FALSE) {
    $out=''; $row=1000;
    while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
      $out.=$processing("A".$row++,$data);
    }
    fclose($handle);
    return TurtlePrefix().$ontologyPrefix.$out;
  }
}

// Generische Transformationsfunktion

function createGenericRDF($subject,$data) {
  $a=array();
  foreach ($data as $key => $value) {
    $fix=fixString($value);
    if (!empty($fix)) $a=addKeyValue($a,"od:predicate".$key,$fix);
  }
  return 
    '<http://leipzig-data.de/Data/CSVValue/'.$subject.">\n\t"
    .join(";\n\t",$a).".\n\n";
}

/* Spezifische Transformationsfunktion für die Akteurs-CVS-Datei.  Zerlegt
   einen CSV-Datensatz in drei Datensätze "Akteur", "AkteurVCard" und
   "AkteurProfil" mit entsprechendem RDF type und URI-Namensschema. */

function createAkteureRDF($subject,$data) {
  $a=array(); // key-values for Akteur
  $b=array(); // key-values for AkteurVCard
  $c=array(); // key-values for AkteurProfil
  $d=array(); // key-values for VCardAddress
  $a[]="a ld:Akteur, org:Organization";
  $a[]="ki:hatAkteurVCard kiv:$subject";
  $a[]="ki:hatAkteurProfil kip:$subject";
  $b[]="a ki:AkteurVCard";
  $b[]="vcard:hasAddress kiva:$subject";
  $c[]="a ki:AkteurProfil";
  $d[]="a vcard:Address";
  $a=addKeyValue($a,"rdfs:label",$data[0]);
  $a=addKeyValue($a,"skos:prefLabel",$data[0]);
  $b=addKeyValue($b,"rdfs:label",$data[0]);
  $c=addKeyValue($c,"rdfs:label",$data[0]);
  $d=addKeyValue($d,"vcard:street-address","$data[1] $data[2]");
  $d=addKeyValue($d,"vcard:postal-code",$data[3]);
  $d=addKeyValue($d,"vcard:locality",$data[4]);
  $a=addKeyValue($a,"ld:hasAddress",
		 createAddress($data[1],$data[2],$data[3],$data[4]));
  $b=addKeyValue($b,"vcard:hasEmail",$data[5]);
  $b=addKeyValue($b,"vcard:hasTelephon",$data[6]);
  $b=addKeyValue($b,"vcard:hasURL",$data[7]);
  $a=addKeyValue($a,"ki:hatAnsprechpartner",$data[8]);
  $a=addKeyValue($a,"ki:hatRolle",$data[9]);
  $d=addKeyValue($d,"vcard:hasGeo",asWKT($data[10]));
  $d=addKeyValue($d,"vcard:hasGeo",asWKT($data[11]));
  $b=addKeyValue($b,"vcard:hasPhoto",$data[12]);
  $c=addKeyValue($c,"ki:hatKurzbeschreibung",$data[13]);

  return 
    "kia:$subject\n\t".join(";\n\t",$a).".\n\n".
    "kiv:$subject\n\t".join(";\n\t",$b).".\n\n".
    "kiva:$subject\n\t".join(";\n\t",$d).".\n\n".
    "kip:$subject\n\t".join(";\n\t",$c).".\n\n";
}

/* Die Daten werden entsprechend den Leipzig Data Bezeichnungsregeln in einer
   RDF-Daten "Akteure.ttl" (Plural) zusammengefasst. */

function createAkteurePrefix() {
  return '
<http://kultur-initiative.net/Data/Akteure/> a owl:Ontology ;
   rdfs:label "Akteure im Leipziger Osten" ;
   cc:license <http://creativecommons.org/publicdomain/zero/1.0/> ;
   cc:attributionURL <http://kultur-initiative.net> ;
   cc:attributionName "Kulturinitiative Leipziger Osten" .

';
}


// ------ helper functions -------

function addKeyValue($a,$key,$value) {
  $value=fixString($value);
  if (empty($value)) return $a;
  if (strpos($value,"http://") !== FALSE ) { // an URI
    $a[]=$key." <$value>"; 
  } else { // a literal
    $a[]=$key." \"$value\""; 
  }
  return $a;
}

function fixString($string) {
  $string=trim($string);
  $string=str_replace("\"","\\\"",$string);
  return $string;
}

/* 
   Geokordinaten sind in den meisten Darstellunge kommaseparierte Paare, was
   mit der Semantik des Kommas in RDF konfligiert, wenn es mehrere
   Geokoordinatenangaben zu einem Subjekt gibt.  Deshalb wird die Darstellung
   von Geokoordinaten als asWKT-String Point(long lat) verwendet. 
*/

function asWKT($string) { 
  $string=trim($string);
  if (empty($string)) return;
  $a=preg_split("/\s*,\s*/",$string); // lat, long
  return "Point($a[1] $a[0])";
}

/* Versuche, eine plausible Adress-URI aus den gegebenen Bestandteilen zu
   erzeugen */

function createAddress($strasse,$nr,$plz,$ort) {
  if (empty($ort)) return;
  $uri=$plz.".".$ort.".".$strasse.".".$nr;
  // mache eine Reihe sinnvoller Ersetzungen
  $uri=preg_replace(array("/\s+/"),array(""),$uri);
  $uri=str_replace(
    array("ä", "ö", "ü", "ß"),
    array("ae","oe","ue","ss"),
    $uri);
  return "http://leipzig-data.de/Data/".$uri;
}

function TurtlePrefix() {
  return '
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix cc: <http://creativecommons.org/ns#> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .
@prefix vcard: <http://www.w3.org/2006/vcard/ns#> .
@prefix org: <http://www.w3.org/ns/org#> .
@prefix skos: <http://www.w3.org/2004/02/skos/core#> .
@prefix geosparql: <http://www.opengis.net/ont/geosparql#> .
@prefix ld: <http://leipzig-data.de/Data/Model/> .
@prefix ki: <http://kultur-initiative.net/Data/Model#> .
@prefix kia: <http://kultur-initiative.net/Data/Akteur/> .
@prefix kiv: <http://kultur-initiative.net/Data/Akteur/VCard/> .
@prefix kiva: <http://kultur-initiative.net/Data/Akteur/VCard/Adresse/> .
@prefix kip: <http://kultur-initiative.net/Data/Akteur/Profil/> .

';
}

// ---- Tests ----

//echo readCSV("../Daten/akteure.csv","","createGenericRDF"); 
echo readCSV("../Daten/akteure.csv",createAkteurePrefix(),"createAkteureRDF"); 

?>


