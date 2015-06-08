<?php

$modulePath = drupal_get_path('module', 'kuenstler');
include $modulePath . '/templates/utils/rest_helper.php';

$friendHTML = <<<EOF
<form method="post" action="?q=kuenstler/menu/Friendpage">
    <div style="float:right">
        <select name="attribute" size="1">
            <option>Name</option>
            <option>Genre</option>
            <option>Hometown</option>
        </select>
        <input class="profile" name="ipsearch" type="text">
        <input class="profile" name="btsearch" value="Suchen" type="submit">
    </div>
</form>
EOF;

if(isset($_GET['id'])){
    $json = file_get_contents($modulePath . '/kuenstler.json', FILE_USE_INCLUDE_PATH);
    $res = json_decode($json,true);
    $friendHTML .= '<div>';
    $friendHTML .= '<h1>Seite von der Gruppe  "'.$res['name'].'"</h1>';
    
    //for($i;$i < count($res);$i++)
    $friendHTML .= '<table>';
    $friendHTML .= '<tr><th width="100">Name:</th><td>'.$res['name'].'</td><tr>';
    $friendHTML .= '<tr><th>Genre:</th><td>'.$res['genre'].'</td><tr>';
    $friendHTML .= '<tr><th>Hometown:</th><td>'.$res['hometown'].'</td><tr>';
    $friendHTML .= '<tr><th>Likes:</th><td>'.$res['likes'].'</td><tr>';
    $friendHTML .= '</table>';
    $friendHTML .= '</table></div>';
}

if(isset($_POST['btsearch']) && strlen ($_POST['ipsearch'])>1)
{
    switch($_POST['attribute']){
        case "Name":
            $searchparams = array( 'name' => $_POST['ipsearch'] );
            break;
        case "Genre":
            $searchparams = array( 'genre' => $_POST['ipsearch'] );
            break;
        case "Hometown":
            $searchparams = array( 'hometown' => $_POST['ipsearch'] );
            break;
    }
    
    
    $return = rest_helper("http://pcai042.informatik.uni-leipzig.de:1570/rest/register/search",$searchparams);

    for($i = 0;$i < count($return);$i++){
        $friendHTML .= '<a href="?q=kuenstler/menu/Friendpage&id='.$return[$i]['id'].'">'.$return[$i]['url'].'</a><br>';
    }
}

