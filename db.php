<?php 
require("http://roomfinder.whf.bz/admin/dbConnect.php");

$result =$conn->query('SELECT * from users');

var_dump($result);