# easteasteast

Drupal System

## Installation

### Requirements

Drupal version 7 (https://www.drupal.org/start.)

### aae_theme

#### Installation

- copy "aaa_theme" to "/drupal/sites/all/themes"
- go to Drupal site
- login to your admin account
- go to "Appearence", select "AAE" and click "Enable and set default“ to activate theme

#### Settings

- in the backend system select "Structure > Blocks"
- add new block
- confirm following settings:

  - Titel 1: < none >
  - Description: Short-Info-Leiste
  - Text (beliebig anpassbar - bitte in HTML schreiben und Links manuell eintragen)     
  - Region: AAE Theme > Footer
  
- in the backend system select "Structure > Blocks"
- select "Main menu"
- confirm following settings:

  - Titel: < none >
  - Region: AAE Theme > Navigation
 
- add (sub)sites to the menu (see Abschlussdokumente/handbuch.pdf)

#### Slider 

- see Abschlussdokumente/handbuch.pdf
  
### aae data

- copy "aae_data" to "/drupal/sites/all/modules"
- edit "db config.php" in directory "aae_data/database"
- login to Drupal admin account
- select group "Custom Modules" and enable "AAE Data"
- call URLs .../?q=akteure or .../?q=events (not working? then reinstall module and try again)

