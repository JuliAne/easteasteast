<?php 

$response = array();

require_once __DIR__ . '/db_connect.php';
$db = new DB_CONNECT();

$result=mysql_query('set names utf8');

if(isset($_POST['name'])) {
	$location = $_POST['name'];
        //Prototype. Just 1 Result. Get along with it.
	$query = "SELECT * FROM localisations WHERE name like '" . $location . "' LIMIT 1";
        $result = mysql_query("set names utf8");
	$result = mysql_query($query);
        //check for existing entry.
        $rowCount = mysql_num_rows($result);
        if($rowCount>0) {
            while($row = mysql_fetch_object($result)) {
                $response["success"] = 1;
                $response["name"] = $row->name;
                $response["lat"] = $row->lat;
                $response["lng"] = $row->lng;
            }
        } else {
            $response["success"] = 0;
            $response["error"] = "No entries to show for " . $location ;
        }

	echo json_encode($response);
} else {
	$response["success"] = 0;
        $response["error"] = "No params";

	echo json_encode($response);
}
?>