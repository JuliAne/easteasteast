<?php
/**
 * profilePage.php generates a HTML Body for Drupal to paste on call. 
 * If the user set any user data before, PHP manages to retrieve those data, and show them.
 * If not, there will be some nice placeholders, and changes will be inserted or updated. Depending 
 * wheater you already set data or not.
 * 
 * 
 * Watte, 11:47 29-01-2015
 */

require_once $modulePath . '/database/db_connect.php';
include $modulePath . '/templates/utils/rest_helper.php';
//Database Object. 
$db = new DB_CONNECT();

//following variables will hold values of db, if any.
$bandname = "";
$mail = "";
$hometown = "";
$genre = "";
//if *Global holds content, it should be 'checked="checked"' which 
//makes the checkbox in the form checked.
$genreGlobal = "";
$hometownGlobal = "";


//following variables are holding sample values for input boxes
$placeholderName = "Name";
$placeholderMail = "Mail";
$placeholderTown = "Town";
$placeholderGenre = "Softrock";



//profilePage.php will also be called if the user chances some properties of his data.
//So, if the necessary value Bandname is set, there's probably a modification
if (isset($_POST['Bandname'])) {

    $bandname = $_POST['Bandname'];
    //following values might be empty. Only bandname is required
    $mail = $_POST['Mail'];
    $hometown = $_POST['Hometown'];
    $genre = $_POST['Genre'];
    if(isset($_POST['genreGlobal'])) {
        $genreGlobal = 'checked';
    }
    if(isset($_POST['hometownGlobal'])) {
        $hometownGlobal = 'checked';
    }
    
    //look up, if there's already a profile saved on db
    $query = "SELECT * FROM profile WHERE PluginType like 'Artist' LIMIT 1";
    $result = mysql_query($query) or die(mysql_error());
    //amount of rows returned by query. differ between 0 and 1
    $amountRows = mysql_num_rows($result);
    
    //if there is no profile saved in db
    if($amountRows == 0) {
        $query = "INSERT INTO profile(Name,Mail,Genre,Town,HometownGlobal,GenreGlobal,PluginType) VALUES ('$bandname', '$mail', '$genre', '$hometown', '$hometownGlobal', '$genreGlobal', 'Artist')";
        mysql_query($query) or die(mysql_error());
    }
    //otherwise update existing profile
    else {
        $updateQuery = "UPDATE profile SET Name='$bandname', Mail='$mail', " .
            "Genre='$genre', Town='$hometown', GenreGlobal='$genreGlobal', HometownGlobal='$hometownGlobal' WHERE PluginType like 'Artist'";
        mysql_query($updateQuery) or die(mysql_error());
    }
    
    //read id from .txt file, if any
    
    if(file_exists('id.txt')) {
        $file = fopen("id.txt", 'r');
        $id = fgets($file);
    } else {
        $id = 0;
    }
    
    //dev: watte runs a local drupal service. the structure is different, so the is 
    //no need for the /drupal/ part.
    if(strpos($_SERVER['HTTP_HOST'], "acquia-drupal")!== false) {
        $url = $_SERVER['HTTP_HOST'] . "/" . $modulePath;
    } else {
        $url = $_SERVER['HTTP_HOST'] . '/drupal/' . $modulePath;
    }
    
    //register on central server
    //see, if genre & hometown is global
    if(strcmp($genreGlobal, 'checked')==0 && strcmp($hometownGlobal, 'checked')==0) {
        $postparams = array(
            'id' => $id, 
            'url' => $url,
            'online' => true, 
            'timestamp' => 42, 
            'name' => $bandname, 
            'role' => "Artist",
            'genre' => $genre,
            'hometown' => $hometown
            
        );
    } else {
        //make only genre global
        if(strcmp($genreGlobal, 'checked')==0) {
            $postparams = array(
                'id' => $id, 
                'url' => $url,
                'online' => true, 
                'timestamp' => 42, 
                'name' => $bandname, 
                'role' => "Artist",
                'genre' => $genre
            );
        } //make only hometown global 
        else {
            if(strcmp($hometownGlobal, 'checked')==0) {
                $postparams = array(
                    'id' => $id, 
                    'url' => $url,
                    'online' => true, 
                    'timestamp' => 42, 
                    'name' => $bandname, 
                    'role' => "Artist",
                    'hometown' => $hometown
                );
            } else {
                //make none of them global
                $postparams = array(
                    'id' => $id, 
                    'url' => $url,
                    'online' => true, 
                    'timestamp' => 42, 
                    'name' => $bandname, 
                    'role' => "Artist"
                );
            }
        }
    }
    $response = rest_helper('http://pcai042.informatik.uni-leipzig.de:1570/rest/register/post', $postparams, 'POST');
    //echo "ID: " . $response['id'];
    if($id != $response['id']) {
        $id = $response['id'];
        $file = fopen("id.txt", 'w');
        fwrite($file, $id);
    }else{
	$file = fopen("id.txt", 'w');
        fwrite($file, $id);
    }
    
} 
//otherwise, if you call the page out of context, try loading data from DB
else 
{
    $query = "SELECT * FROM profile WHERE PluginType like 'Artist' LIMIT 1";
    $result = mysql_query($query) or die(mysql_error());
    
    //if there's something to load from db, do so.
    if(mysql_num_rows($result)==1) {
        while ($row = mysql_fetch_object($result)) {
            $bandname = $row->Name;
            $mail = $row->Mail;
            $genre = $row->Genre;
            $hometown = $row->Town;
            $genreGlobal = $row->GenreGlobal;
            $hometownGlobal = $row->HometownGlobal;
        }
    } 
}

$pathThisFile = $_SERVER['REQUEST_URI']; 

$profileHTML = <<<EOF
<form action='$pathThisFile' method='POST'>
    <label>Bandname:</label>
    <input type="text" class="profile" id="profileBandnameInput" name="Bandname" value="$bandname" placeholder="$placeholderName" required>
    <label>Mail:</label>
    <input type="email" class="profile" id="profileMailInput" name ="Mail" value="$mail" placeholder="$placeholderMail">
    <label>Genre:</label>
    <input type="text" class="profileGenre" id="profileGenreInput" name ="Genre" value="$genre" placeholder="$placeholderGenre">
        <input type="checkbox" class="genreProfileBox" name="genreGlobal" value="genreGlobal" $genreGlobal>Make your genre global
    <label>Hometown:</label>
    <input type="text" class="profileHometown" id="profileHometown" name="Hometown" value="$hometown" placeholder="$placeholderTown">
        <input type="checkbox" class="hometownProfileBox" name="hometownGlobal" value="hometownGlobal" $hometownGlobal>Make your hometown global

    <input type="submit" class="profile" id="profileSubmit" value="Submit">
</form>
EOF;

