<?php

$severname = "localhost";
$username = "root";
$password = "";
$dbname = "final_project";

$conn = mysqli_connect($severname, $username, $password, $dbname);
if(!$conn){
    die("Connection Faild". mysql_connect_error());
}

?>