<?php

require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
require_once __DIR__ .'/../models/CodigoUnico.php';

class MesaController extends Mesa {

    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        // Creamos la mesa
        $mesa = new Mesa();
        $mesa->cod_mesa = CrearCodigo();
        $mesa->foto = $parametros['foto'];
        $mesa->estado = Mesa::CERRADA;
        $payload = $mesa->CrearMesa();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::ListarMesas();
        $payload = json_encode(array("lista_Mesas" => $lista));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}