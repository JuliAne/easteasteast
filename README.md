# easteasteast

## THIS IS AN ARCHIVE! You can find the current version of Leipziger Ecken [here](https://github.com/Leipziger-Ecken/drupal).

*Deine soziale Stadtteilplattform powered by Drupal.*
Alpha, alpha everywhere. If you'd like to join us take a look @ https://leipziger-ecken.de/faq

## Installation

### Requirements

- PHP 5.4 or better with Imagick-extension being installed.
- Drupal version 7.x (https://www.drupal.org/download) with "CKeditor", "Views" & "Messages"-module being installed
- Recommended modules for an advanced UX/SEO: "HTML5 Tools", "Metatags" & "AdvAgg"
- Enjoy extra-features with our support for "Simple FB Connect"-, "aggregator"- and "xmlsitemap"-module.
- MySQL/MariaDB with support for datetime-fields and -actions (e.g. CURDATE())

### Installation via git:

Within your local Drupal-copy, clone this repo to 'sites/all/'. The module & theme will now be manageable from within Drupal's backend.

### aae_theme

#### Installation via (S)FTP

- Copy "themes/aaa_theme" to "drupal/sites/all/themes"
- Login to your Drupal-backend
- Navigate to "Appearence", select "AAE Theme" and click "Enable and set default“ to activate the theme.

#### Settings (untested)

- Add a new block @ "Structure > Blocks"
- Confirm the following settings:

  - Title: <none>
  - Description: Short-Info-Leiste
  - Text (beliebig anpassbar - bitte in HTML schreiben und Links manuell eintragen)
  - Region: AAE Theme > Footer

- Set the new menu @ "Structure > Blocks > Main Menu"
- Confirm the following settings:

  - Title: <none>
  - Region: AAE Theme > Navigation

- Add (sub)sites and paths (e.g. /events, /akteure) to the menu.

### aae data

- Copy "aae_data" to "/drupal/sites/all/modules/"
- Edit "aae_data_helper.php" and set the internal server-path's & other variables
- Login to your Drupal-backend
- Select group "Custom Modules" and enable "AAE Data"
- call URLs .../akteure or .../events (not working? then reinstall module and try again)


### Featured modules

*XMLSITEMAP*: AAE Data module creates paths which can easily be used to create a XML sitemap. To do so, download "XML Sitemap"-module and activate it together with "XML Sitemap menu". After configuring, navigate to Drupal > Structure > Menues > "Navigation" > Edit and set "Inclusion" to "Included". On "Link lists"-Tab, deactivate unused or non-pulic paths (e.g. "events/new"). Mind the fact that this module uses  Cronjobs - it can take days to receive an updated sitemap.

#### Issues

The performance of Drupal can vary immensively depending on your webhost. Take a look @ http://drupal.stackexchange.com/
to receive further support. Pro-tips: Change the DB-Host in settings.php
from "localhost" to "127.0.0.1" and activate Drupal's internal cache or use memcache to gain a better performance.
