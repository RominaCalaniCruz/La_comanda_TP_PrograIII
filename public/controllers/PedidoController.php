<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/CodigoUnico.php';
require_once './models/Mesa.php';
require_once './models/Producto.php';
class PedidoController extends Pedido
{

  public function CrearUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    // Creamos el pedido
    $pedido = new Pedido();
    $cod_mesa = $parametros['cod_mesa'];

    $pedido->fecha_pedido = date("Y-m-d H:i:s");
    $pedido->estado = Pedido::PENDIENTE;
    $pedido->mozo = $parametros['id_mozo'];
    $pedido->nombre_cliente = $parametros['nombre_cliente'];
    $pedido->producto = $parametros['id_producto'];

    $mesa = Mesa::ListarUna($cod_mesa);
    if ($mesa->estado == Mesa::CERRADA){
      
      $pedido->cod_pedido = CrearCodigo();
      $mesa->estado = Mesa::ESPERANDO;
      $mesa->modificar();
    }
    else{
      $pedido->cod_pedido = Pedido::traerCodPedido($cod_mesa);
    }
    $pedido->cod_mesa = $cod_mesa;
    $pedido->crearPedido();

    $payload = json_encode(array("mensaje" => "Pedido creado con exito".$mesa->estado));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodos();
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function IniciarUnPedido($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idEmp = $parametros['id_emp'];
    $rolEmp = $parametros['rol'];
    $idPedido = $parametros['id_pedido'];
    $tiempo = $parametros['tiempo_estimado'];
    $pedido = Pedido::obtenerUno($idPedido);
    if($pedido){
      // var_dump($pedido);
      if($rolEmp == Producto::traerCategoriaProd($pedido->producto)){
        Pedido::comenzar($idEmp, $idPedido, $tiempo);
        $payload = json_encode(array("mensaje" => "Pedido iniciado con exito"));
  
      }
      else{
        $payload = json_encode(array("mensaje" => "este pedido no es de su sector  "));
      }

    }
    else{
      $payload = json_encode(array("mensaje" => "este pedido no existe"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function TerminarUnPedido($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idPedido = $parametros['id_pedido'];
    $rolEmp = $parametros['rol'];
    $pedido = Pedido::obtenerUno($idPedido);
    if($pedido){
      if($rolEmp == Producto::traerCategoriaProd($pedido->producto)){
        $payload = json_encode(array("mensaje" => "Pedido finalizado con exito"));
        Pedido::finalizarPedido($idPedido);
      }
      else{
        $payload = json_encode(array("mensaje" => "este pedido no es de su sector"));
      }
    }
    else{
      $payload = json_encode(array("mensaje" => "este pedido no existe"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function ListarPedidosPendientes($request, $response, $args)
  {
    $lista = Pedido::listarPendientes($args['rol']);
    $payload = json_encode(array("lista_Pedidos_Pendientes" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
