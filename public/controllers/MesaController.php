<?php

require_once './models/Mesa.php';
require_once './models/Pedido.php';
require_once './models/PedidoProducto.php';
require_once './interfaces/IApiUsable.php';
require_once __DIR__ . '/../models/CodigoUnico.php';

class MesaController extends Mesa
{

  public function CargarUno($request, $response, $args)
  {
    $mesa = new Mesa();
    $mesa->cod_mesa = CrearCodigo();
    $mesa->estado = Mesa::CERRADA;
    $mesa->CrearMesa();
    $payload = json_encode(array("mensaje" => "mesa creada"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::ListarMesas();
    $payload = json_encode(array("lista_Mesas" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function CerrarMesa($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $cod_mesa = $parametros["cod_mesa"];
    Mesa::ActualizarEstado($cod_mesa,Mesa::CERRADA);    
    $payload = json_encode(array("mensaje" => "mesa cerrada"));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function MesaMasUsada($request, $response, $args)
  {
    $resultado = Mesa::MasUsada();    
    if(empty($resultado)){
      $payload = json_encode(array("error" => "no se encontro"));
    }
    else{
      $payload = json_encode(array("mesa_mas_usada" => $resultado));
    }
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
