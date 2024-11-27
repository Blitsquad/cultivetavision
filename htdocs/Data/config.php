<?php

$serveur = "localhost";
$database = "blog2";
$user = "root";
$pwd = "";

try{
    $connexion = new PDO("mysql:host=$serveur;dbname=$database", $user, $pwd);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion à la base OK ";
} catch (PDOException $e){
    echo "FAIIIL"; 
}


?>