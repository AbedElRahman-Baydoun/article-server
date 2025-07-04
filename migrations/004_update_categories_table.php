<?php 
require("../connection/connection.php");


$query = "ALTER TABLE categories....";

$execute = $mysqli->prepare($query);
$execute->execute();