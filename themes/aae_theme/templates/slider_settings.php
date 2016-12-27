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

$blocks = new Drupal\AaeData\aae_blocks();
$counts = $blocks->count_projects_events();

$sliders = array(

 $slider1 = array(
   'image' => 'ostspiele_fussball_ida.jpg'
 ),

 /*$slider2 = array(
   'image' => 'slider_bg.jpg',
 ),*/

 $slider3 = array(
   'image' => 'slider_2_min.jpg',
 ),

 /* Only for the bg_image: */

  $slider4 = array(
   'image' => 'rabet_abends_ida.jpg',
   'headline' => '<strong>'.$counts['akteure'].'</strong> Akteure. <strong>'.$counts['events'].'</strong> Events.<br /> Eine Plattform.',
   'description' => '<p>Jetzt anmelden, Projekt einstellen oder einfach nur mitmischen.</p>',
   'whiteText' => true,
   'blueButton' => array(
     'text' => '<span>Jetzt registrieren!</span>',
     'link' => base_path().'nutzer/register'
   )
 )

);

?>
