<?php
// Importar conexcion
require 'includes/config/database.php';
$db = conectarDB();

// Crear un email y un pasword
$email = 'correo@correo.com';
$pasword = '123456';
// Hashear password's
$paswordHash = password_hash($pasword, PASSWORD_DEFAULT);

var_dump($paswordHash);
// Query para crear al usuario
$query = "INSERT INTO usuarios (email, password) VALUES ('${email}', '${paswordHash}'); ";
// echo $query;


// Agregar a la base de datos
mysqli_query($db, $query);

