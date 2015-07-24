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

$sliders = array(

 $slider1 = array(
   'image' => 'slider_bg.jpg',
   'headline' => 'Den Leipziger Osten neu entdecken',
   'description' => 'Deine Plattform für den ganzen Stadtteil: Lerne kreative Projekte aus Deiner Umgebung kennen & erfahre, wann und wo sich etwas in Deinem Bezirk bewegt!<br />
<br /><strong>Kostenlos. Offen. Lokal.',
   'whiteText' => true,
   'blueButton' => array(
     'text' => 'Mehr erfahren...',
     'link' => '#'
    )
 ),

 $slider2 = array(
   'image' => 'slide1.jpg',
   'headline' => '<strong>32</strong> Projekte. <strong>50</strong> Events. Eine Plattform.',
   'description' => 'Jetzt anmelden, Projekt einstellen oder einfach nur mitmischen.',
   'whiteText' => true,
   'blueButton' => array(
     'text' => 'Registrieren',
     'link' => base_path().'?q=user/register'
   )
 )

);

?>
