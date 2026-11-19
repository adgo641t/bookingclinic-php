<?php
try{
$dbhost = 'localhost';
$dbname = 'booking_system';
$dbuser = 'root';
$dbpass = '';

$dsn = "mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4";

$pdo = new PDO($dsn, $dbuser, $dbpass, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
));

}catch(PDOException $e){
    echo "Error!: ". $e->getMessage(). "<br/>";
}
?>