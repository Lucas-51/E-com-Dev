<?php
$host = 'localhost';
$db   = 'ecom'; // Remplacez par le nom de votre base
$user = 'root'; // Par défaut sous MAMP
$pass = 'root'; // Par défaut sous MAMP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>
