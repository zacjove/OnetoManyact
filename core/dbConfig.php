<?php  

$host = "localhost";
$user = "root";
$password = "";
$dbname = "transport_app";
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

$pdo = new PDO($dsn, $user, $password, [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

?>