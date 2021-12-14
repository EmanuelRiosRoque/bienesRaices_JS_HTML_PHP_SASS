<?php

function conectarDB() : mysqli {
    $db = mysqli_connect('localhost', 'root', '', 'bienes_raices');

    if (!$db) {
        echo 'Erro no se pudo conectar';
        exit;
    }

    return $db;

}