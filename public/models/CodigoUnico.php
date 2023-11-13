<?php

function CrearCodigo()
{
    $long = 5;   
    $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $codigoUnico = "";
    for ($i = 0; $i < $long; $i++) {
        $codigoUnico .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
//validar que no se haya repetido en BDD
    return $codigoUnico;
}
