<?php
require_once './models/Pedido.php';
require_once './models/PedidoProducto.php';
require_once './interfaces/IApiUsable.php';
require_once './models/CodigoUnico.php';
require_once './models/Mesa.php';
require_once './models/Producto.php';
require_once './models/Empleado.php';
require_once __DIR__ .'/../middlewares/AutentificadorJWT.php';
use Slim\Http\UploadedFile;

class PedidoController extends Pedido
{

  public function CrearUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $arrayProductos = $parametros['productos'];
    $pedido = new Pedido();
    $pedido->nombre_cliente = $parametros['nombre_cliente'];
    $pedido->fecha_pedido = date("Y-m-d H:i:s");
    $pedido->estado = Pedido::PENDIENTE;
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);
    $pedido->mozo = (Empleado::obtenerEmpleado($data->usuario))->id;
    var_dump($pedido);
    $pedido->cod_pedido = CrearCodigo();
    $pedido->cod_mesa = $parametros['cod_mesa'];
    
    $pedido->crearPedido();

    $pedidoProducto = new PedidoProducto();
    $pedidoProducto->cod_pedido = $pedido->cod_pedido ;
    $pedidoProducto->estado = Pedido::PENDIENTE;
    foreach ($arrayProductos as $uno) {
      $pedidoProducto->id_producto = $uno['id_producto'];
      $pedidoProducto->cantidad = $uno['cantidad'];
      $pedidoProducto->crearPedidoProducto();
    }

    $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function SubirFotoMesa($request, $response, $args){
    $parametros = $request->getParsedBody();
    $foto_mesa = $request->getUploadedFiles()['foto_mesa'];
    $fotoOriginal = $foto_mesa->getClientFilename();

    $cod_pedido = $parametros['cod_pedido'];
    $nombreFoto = $cod_pedido;

    $extension = pathinfo($fotoOriginal, PATHINFO_EXTENSION);
    $rutaDestino = './fotos/'.$nombreFoto.'.'.$extension;

    $foto_mesa->moveTo($rutaDestino);

    Pedido::cargarFoto($cod_pedido,$rutaDestino);
    
		$payload = json_encode(array("mensaje" => "Foto agregada con exito"));
		$response->getBody()->write($payload);

		return $response->withHeader('Content-Type', 'application/json');
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
    $idPedido = $parametros['id_pedido'];
    $tiempo = $parametros['tiempo_estimado'];
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);
    $idEmp = (Empleado::obtenerEmpleado($data->usuario))->id;

    PedidoProducto::tomarPedidoProducto($idPedido,$tiempo,$idEmp);
    $cod_pedido = PedidoProducto::traerCodxId($idPedido);

    if(PedidoProducto::verEstadoGrupal($cod_pedido)){
      Pedido::actualizarEstado($cod_pedido,Pedido::PREPARACION);
      Pedido::actualizarPedido($cod_pedido);
    }
    $payload = json_encode(array("mensaje" => "actualizado estado"));
    

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
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
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);
    
    $lista = Pedido::listarPendientes($data->rol);

    $payload = json_encode(array("lista_Pedidos_Pendientes" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
