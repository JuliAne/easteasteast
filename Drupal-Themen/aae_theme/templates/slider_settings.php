<?php

/**
*
* -- Einstellungen für den Startseite-Slider --
*
* Ein $slider-Listenelement = Ein horizontaler Slider
* Verwendung von HTML möglich - zur Trennung von Absätzen <br /> verwenden
*
* Mögliche Parameter (sonst jeweils default = 0):
*
*  'image' [Pfad.Dateiendung] = Hintergrundbild, welches in themes/aae/img zu liegen hat.
*  'headline' [string] = Überschrift (<h1>)
*
*  'description' [string] = Beschreibungstext (<p>)
*
*  'whiteText' [boolean] = Soll der Text in weiß dargestellt werden?
*
*  'blueButton' [array] = Zeige blauen Button an.
*     @example 'bluebutton' = array('text' => 'lorem ipsum', 'link' => '/events/all');
*
*  'whiteButton' [array] = s. bluebutton
*
*/

require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';

$counts = block_aae_count_projects_events();

$sliders = array(

 $slider1 = array(
   'image' => 'slider_bg.jpg',
   'headline' => 'Den Leipziger Osten neu entdecken',
   'description' => 'Deine Plattform für den ganzen Stadtteil: Lerne Akteure aus Deiner Umgebung kennen & erfahre, wann und wo sich etwas in Deinem Bezirk bewegt.<br />
<br /><strong>Kostenlos. Offen. Lokal.</strong>',
   'whiteText' => true,
   'blueButton' => array(
     'text' => 'Mehr erfahren...',
     'link' => base_path().'leipziger-osten'
    )
 ),

 $slider2 = array(
   'image' => 'slider_2_min.jpg',
   'headline' => '<strong>'.$counts['akteure'].'</strong> Akteure. <strong>'.$counts['events'].'</strong> Events:<br /> Eine Plattform.',
   'description' => 'Jetzt anmelden, Projekt einstellen oder einfach nur mitmischen.',
   'whiteText' => true,
   'blueButton' => array(
     'text' => '<span style="color:white;">Jetzt registrieren!</span>',
     'link' => base_path().'user/register'
   )
 )

);

?>
