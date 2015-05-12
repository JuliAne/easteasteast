<?php
/**
 * User: Immanuel Plath
 * Date: 19.01.15
 */

// include section
//=========================
require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

// Class
// Class works a wrapper for EasyRDF Graph and offer possibility to add new service functions
// param: uri from a location
// param: language example "de" or "en" ...
// return: a valid existing language
//=========================
class RdfGraph
{
    // Class variables
    private $graph;
    
    // function
    // function constructor
    // param: Array contains path to files which contain rdf data that should load into rdf graph
    // return: none ...
    //=========================
    public function __construct($pathToData)
    {
        // Set RDF namespaces
        EasyRdf_Namespace::set('ldo', 'http://leipzig-data.de/Data/Ort/');
        EasyRdf_Namespace::set('ldp', 'http://leipzig-data.de/Data/Person/');
        EasyRdf_Namespace::set('ldtag', 'http://leipzig-data.de/Data/Tag/');
        EasyRdf_Namespace::set('ld', 'http://leipzig-data.de/Data/Model/');
        EasyRdf_Namespace::set('sysont', 'http://ns.ontowiki.net/SysOnt/');
        EasyRdf_Namespace::set('xsd', 'http://www.w3.org/2001/XMLSchema#');
        EasyRdf_Namespace::set('jsp1', 'http://localhost/jsp/');
        EasyRdf_Namespace::set('jsp', 'http://leipzig-data.de/Data/Jugendstadtplan/');
        //construct graph
        $this->graph = new EasyRdf_Graph("http://leipzig-data.de/Jugendstadtplan/");
        //load data
        foreach ($pathToData as $data) {
            if (file_exists($data)) {
                $this->addRdfData($data);
            }
        }
    }
    
    // function
    // function return EasyRDF Graph
    // param: none params ...
    // return: full EasyRDF Graph
    //=========================
    public function getGraph()
    {
        //$this->graph = $newval;
        return $this->graph;
    }
    
    // function
    // function add new RDF ressources to Rdf Graph
    // param: path to file contain rdf data
    // return: none ...
    //========================= 
    public function addRdfData($pathToData)
    {
        $this->graph->parseFile($pathToData);
    }
    
    // function
    // function add all rdf files from a folder to graph
    // param: path to folder contains data
    // param: Array of file extensions which will be parsed (default json and ttl)
    // return: a valid existing language
    //=========================  
    public function addRdfFolder($pathToData, $datatypes)
    {
        $files = scandir($pathToData);
        if (count($datatypes) == 0) {
            $datatypes = array(
                "json",
                "ttl"
            );
        }
        for ($i = 0; $i < count($files); $i++) {
            $file = pathinfo($files[$i]);
            if (in_array($file["extension"], $datatypes)) {
                $this->addRdfData($pathToData . $files[$i]);
            }
        }
    }
    
}

?>