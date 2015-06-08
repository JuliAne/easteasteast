<?php

require_once __DIR__ . '/db_connect.php';

$db = new DB_CONNECT();

$query = "UPDATE profile SET AmountLikes = " . $amountLikes;
mysql_query($query);
$error = mysql_error();