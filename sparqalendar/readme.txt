== RDF2WP ==
Contributors: swp11-6
Donate Link: http://pcai042.informatik.uni-leipzig.de/~swp11-6/index.html
Tags: linked data, semantics, table, rdf, n3, turtle, xml, catalogus professorum lipsiensis, arc2, sparql, ksc, editor
Requires at least: 2.7
Tested up to: 3.1.2
Stable tag: 1.0

== Description ==

Das Plugin bestitzt 3 Hauptkomponenten:

Eine Import und Update Schnittstelle über die man RDF einem Blogeintrag hinzufügen kann und verändern.
Das Plugin ermöglicht das Editieren von RDF Daten und es publiziert die Daten als RDF und LinkedData.
Über ein einfaches Template System können zu einem Blogeintrag zusätzliche Informationen aus einem       semantischen Triple Store mit SPARQL geholt und angezeigt werden.

== Installations- und Konfigurationsanleitung ==

-	Installation Plugin:
Die Installation des RDF2WP Plugins entspricht der Standard- Plugin- Installation in Wordpress. 
Sollte Ihnen das Plugin in einer .zip- Datei vorliegen, so kann es über die WordPress Schnittstelle im Admin-Bereich installiert werden. Gehen Sie dafür in das Settings-Menü „Plugins“ Eintrag „Add New“ und wählen in der oberen Link-Leiste den Eintrag Upload. Hier kann der .zip Ordner von der lokalen Festplatte hochgeladen und installiert werden.
Anderenfalls lässt sich das Plugin durch einfaches Kopieren des Plugin Ordners „RDF2WP“ in den Plugin Ordner Ihrer Lokalen Wordpress Installation hinzufügen. Dieser befindet sich in den häufigsten Fällen unter „<Pfad zur WP-Installation>/wordpress-<version>/wp-content/plugins“.

Möchten Sie einige der Pakete nicht nutzen, so können Sie diese in der Datei RDF2WP.php in den Zeilen 11 bis 53 auskommentieren. So kommentieren Sie z.B. das Widget Paket aus:
Zeile 18: //include_once('Widget/KSCWidget.php');
Zeile 19: //$widget = new KSCWidget();
Welcher Teil zu einem Package gehört, ist dort ausdrücklich kommentiert.

-	Konfiguration Plugin
Im Folgenden wird beschrieben wie sie das Plugin und seine Komponenten (Packages) an ihre Anforderungen Anpassen und entsprechende Konfigurationen vornehmen.

1.	Package Import

Inhalt:
Das Import- Package bezieht sich auf den Import des Professorenkatalogs der Universtität Leipzig, ist aber durchaus in der Lage RDF Ressourcen aus anderen Endpunkten in die Wordpress Installation einzubinden. 

WordPress- Pfad:
Die Importschnittstelle befindet sich im Admin- Bereich Menü- Eintrag „Settings“ Unterpunkt „Import Professorum Catalogum Lipsiensis“.

Konfiguration:
Die Importseite gliedert sich in drei Bereiche. 
Im obersten Bereich werden die Einstellungen für den Import vorgenommen. Definieren Sie hier den SPARQL- Endpunkt an den Ihre Anfrage gestellt werden soll und die SPARQL- Anfrage selbst. Des Weiteren die maximale Anzahl der zu importierenden Ressourcen mit LIMIT. Wird kein Limit angegeben, so werden alle Ressourcen die sich im Ergebnis befinden importiert. Beachten Sie hierbei, dass das Interface nur Anfragen die sich auf eine RDF Klasse beziehen, wie es beim Professorenkatalog die Professoren sind, ordnungsgemäß importiert. Von dieser Klasse können außerdem beliebig viele Prädikate abgefragt werden. Abfragen ähnlich, die des „Graphical Query Builders“  sind somit kein Problem. Von jeder Instanz der RDF- Klasse wird ein 
Post mit dem gesamten Inhalt der Ressource erstellt. Für die angefragten Attribute werden jeweils eigene Kategorien erstellt. Alle Instanzen der angegebenen Klasse werden in eine Oberkategorie mit dem Namen dieser Klasse importiert. Alle erstellten Kategorien werden in einer Oberkategorie abgelegt. Ihr Standardname ist „Catalog“. Der Name lässt sich auf einfache Weise anpassen, indem in der Datei CategoryCreator.php die Konstante „mainCategorie“ auf den gewünschten Namen gesetzt wird. Im Weiteren Definieren Sie in welche Weise die Posts in WordPress erstellt werden sollen. Jede Änderung muss 
mit dem „Save Changes“ Button bestätigt werden, wobei der Enpunkt und die Query auf Korrektheit geprüft werden.
Im mittleren Bereich wird der Import, im unteren Bereich das Löschen, wie dort beschrieben durchgeführt.

Plugin-Pfad:
RDF2WP/Import/

Empfehlung:
Es wird empfohlen das Collapsing Categories Plugin  zu installieren, um eine hierarchische Darstellung der erzeugten Kategorien zu ermöglichen.

Ressourcen:
GraphicalQueryBuilder
http://catalogusprofessorum.org/graphicalquerybuilder/display/position/3/active?m=http%3A%2F%2Fcatalogus-professorum.org
%2Flipsiensis%2F
CollapsingCategories
http://wordpress.org/extend/plugins/collapsing-categories/

2.	Package Widget

Inhalt:
Mit dem Widget-Package wird die Anzeige von RFD Daten in Wordpress ermöglicht.  Ein frei konfigurierbares Widget zeigt 
immer die aktuellen Informationen in der Widget-Sidebar an.

WordPress- Pfad:
Die Einstellungen zum Widget befinden sich im Admin- Bereich Menü- Eintrag „Appearance“ Unterpunkt „Widgets“.

Konfiguration:
Damit das Widget angezeigt wird, muss es im Sidepad sein. Ziehen Sie dazu das RDF Widget in das Sidepad per DragNDrop. Nun 
können Sie folgende Einstellungen vornehmen:
Title – Titel des Widgets. Dieser wird über dem Widget angezeigt.
SPAQRL Endpoint – Der Endpunkt an dem die SPAQRL Anfrage gesendet wird.
Categories – eine Liste der Top-Level Kategorien in Wordpress unter welchen das Widget angezeigt werden soll. Die einzelnen Kategorien werden durch ein Komma getrennt. Falls Sie dieses Feld frei lassen, so ist das Widget uneingeschränkt sichtbar.
Sollten Sie ein Widget erstellen wollen, welches nur auf der Hauptseite sichtbar sein soll, so Klammern Sie in der Datei KSCWidget.php den Kommentar in der Funktion „widget“ ein und die darunterliegende Zeile aus. Wird das Kategorie Feld freigelassen, so werden die Widgets nun nur noch auf der Blog-Hauptseite angezeigt.
Query – die SPAQRL Anfrage, welche die Daten für das Widget festlegt.
Nachdem Sie die nötigen Einstellungen vorgenommen haben, müssen Sie diese nur noch speichern. Klicken Sie dazu auf den Save Button.

Plugin-Pfad:
RDF2WP/Widget/

3.	Package Output

Inhalt:
Das Output-Package sorgt für drei Dinge. Dafür, dass RDF Daten welche sich in einem Post befinden, besucherfreundlich angezeigt werden, es stellt eine Schnittstelle für den Export von RDF Daten bereit. Und es bringt einen RDF Parser mit sich, welcher die Daten validiert und dem Verfasser bei Fehlern hilfreiche Hinweise auf die Ursache liefert.

Anzeige von RDF Daten:
In einem Post werden die RDF Daten zwischen den „semantics“ Tags gespeichert:
[semantics format=““] Hier RDF Daten [/semantics].
Zu beachten ist das Richtige Format der Daten. Neben den Werten "turtle", "rdfxml" und "json" sind noch folgende Werte für das Format vorgesehen:
"infobox" – von dem Klappstuhlclub verwendete Syntax
"prof" – ein Alias für die Turtle Syntax, wird für den Professorenkatalog verwendet.
Bei der Anzeige des Posts werden diese Daten automatisch in Tabellenform dargestellt.
Verwendete Präfixe für neue RDF-Daten in einem Post können über das Menü Settings, RDF Einträge hinzugefügt werden.

Export von RDF Daten:
Die RDF Daten können aus jedem Post als Linked Data in verschiedenen Formaten angefragt werden. Dazu wird einfach der gewünschte Typ im HTTP Accept Header angegeben. Folgende Datentypen werden unterstützt: application/rdf+xml, application/json+rdf, text/plain, text/rdf+n3, text/turtle.

Plugin-Pfad:
RDF2WP/Output/

4.	Package Editor

Inhalt:
Das Editor-Package bringt ein bequemes Template System mit sich. Für eine schnelle  Eingabe von semantischen Daten in einen Post, werden zwei Buttons dem Editor hinzugefügt. Diese sind für die Klappstuhlclub Mitglieder interessant. Allerdings können die Templates auch angepasst werden.
Das Anpassen eines Templates:
Um z.B. das Meeting-Template anzupassen, müssen Sie eine kleine Veränderung der Datei MeetingTemplateDialog.php vornehmen. Dazu öffnen Sie bitte diese Datei in einem Texteditor Ihrer Wahl. In den Zeilen 21 bis 28 sehen Sie schon alles was Sie brauchen.
Bsp. Zeile 21:
$template->addEntry('| nummer = "300"', true);
Der erste Parameter '| nummer = "300" ist eine Textzeile, welche später beim anklicken des Buttons dem Editor hinzugefügt werden soll.
Der zweite Parameter gibt an, ob diese Zeile  in der Checkbox des Templates vorab ausgewählt werden soll. true bedeutet 
Sie wird ausgewählt, false hingegen heißt bei dieser Zeile wird das Häkchen nicht gesetzt.
Wollen Sie eine neue Zeile hinzufügen, so kopieren Sie eine der Zeilen 21 bis 28 und fügen Sie diese an.
So kann z.B. Ihre neue Zeile aussehen:
$template->addEntry('| essen = "altes Brötchen", "Pflaume"', false);

Plugin-Pfad:
RDF2WP/Editor/
