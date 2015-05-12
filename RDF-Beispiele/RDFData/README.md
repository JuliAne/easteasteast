jugendstadtplan.json is almost the source used in the 2013 version of the
Jugendstadtplan. It was only missing a definition for jsp:Bildung.

The original version of jugendstadtplan.json was transformed with trans.php to
the JSP-13.ttl turtle file, the required information about jsp:Bildung added
and then again transformed into jugendstadtplan.json JSON file with rapper:

rapper -g JSP.ttl -o json 

How to prettyprint json file: 
cat jugendstadtplan.json |python -mjson.tool

The remaining data 

Adress-Auswahl.ttl 
GeoDaten.ttl 
Orte.ttl 
Orts-Auswahl.ttl 
Jugendstadtplan.ttl 

were extracted from a more recent version of the Leipzig Data collection.  Its
an open task to update the application to that data. 

Queries.txt is a sample file with useful SPARQL queries.
