# easteasteast

## Installation

### Requirements

*Drupal version 7.x (https://www.drupal.org/start.)*
*Imagick*
*MySQL/MariaDB*

### Installation via git:

Within your local drupal-copy, clone this repo to '/sites/all/'. The module & theme will now be managable in the backend.

### aae_theme

#### Installation via (S)FTP

- Copy "themes/aaa_theme" to "/drupal/sites/all/themes"
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

- Add (sub)sites to the menu (see Abschlussdokumente/handbuch.pdf)

#### Slider-Settings (front-page)

- See Abschlussdokumente/handbuch.pdf

### aae data

- Copy "aae_data" to "/drupal/sites/all/modules/"
- Edit "aae_data_helper.php" and set the internal server-path's
- Login to Drupal admin account
- Select group "Custom Modules" and enable "AAE Data"
- call URLs .../akteure or .../events (not working? then reinstall module and try again)
