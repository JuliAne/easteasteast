# easteasteast

*Deine soziale Stadtteilplattform powered by Drupal.*
Alpha, alpha everywhere. If you'd like to join us take a look @ https://leipziger-ecken.de/faq

## Installation

### Requirements

- PHP 5.4 or better
- Drupal version 7.x (https://www.drupal.org/download) with "CKeditor"-module being installed
- Recommended modules for an advanced UX: "HTML5 Tools", "Simple FB Connect" (supported), "Views" & "Metatags"
- Imagick
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

#### Issues

The performance of Drupal can vary immensively depending on your webhost. Take a look @ http://drupal.stackexchange.com/
to receive further support. Pro-tips: Change the DB-Host in settings.php
from "localhost" to "127.0.0.1" and activate Drupal's internal cache or use memcache to gain a better performance.
