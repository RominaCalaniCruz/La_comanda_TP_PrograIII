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

class PedidoController
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

    $pedido->cod_pedido = CrearCodigo();
    $pedido->cod_mesa = $parametros['cod_mesa'];
    
    var_dump($pedido);
    $pedido->crearPedido();

    $pedidoProducto = new PedidoProducto();
    $pedidoProducto->cod_pedido = $pedido->cod_pedido ;
    $pedidoProducto->estado = Pedido::PENDIENTE;
    Mesa::ActualizarEstado($pedido->cod_mesa,Mesa::ESPERANDO);
    foreach ($arrayProductos as $unProd) {
      $pedidoProducto->id_producto = $unProd['id_producto'];
      $pedidoProducto->cantidad = $unProd['cantidad'];
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
    if(PedidoProducto::verEstadoGrupal($cod_pedido,Pedido::PREPARACION)){
      Pedido::ActualizarEstado($cod_pedido,Pedido::PREPARACION);
      Pedido::ActualizarTiempoEstimado($cod_pedido);
    }
    $payload = json_encode(array("mensaje" => "actualizado estado"));
    

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function TerminarUnPedido($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $idPedido = $parametros['id_pedidoProd'];

    PedidoProducto::finalizarPedidoProducto($idPedido);
    $cod_pedido = PedidoProducto::traerCodxId($idPedido);
    if(PedidoProducto::verEstadoGrupal($cod_pedido,Pedido::LISTO)){
      Pedido::actualizarPedidoListo($cod_pedido);
    }
    $payload = json_encode(array("mensaje" => "Pedido finalizado con exito"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function ListarPedidosPendientes($request, $response, $args)
  {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);
    
    $lista = Pedido::ListarXEstadoYRol($data->rol,Pedido::PENDIENTE);

    $payload = json_encode(array("lista_Pedidos_Pendientes (".$data->rol.")" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function CalcularImporte($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $cod_pedido = $parametros['cod_pedido'];
    $resultado = PedidoProducto::CalcularImporteFinal($cod_pedido);
    if(!empty($resultado)){
      $cod_mesa = (Pedido::obtenerUno($cod_pedido))[0]["cod_mesa"];
      $importe = $resultado[0]["importeFinal"];
      Pedido::ActualizarImporteAPagar($cod_pedido,$importe);
      Pedido::ActualizarEstado($cod_pedido,Pedido::FINALIZADO);
      Mesa::ActualizarEstado($cod_mesa,Mesa::PAGANDO);
      $payload = json_encode(array("mensaje" => "Pedido finalizado con exito, el cliente pagara: ".$importe));
    }
    else{
      $payload = json_encode(array("mensaje" => "pedido no encontrado"));

    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function Entregar($request, $response, $args)
  {
    $cod_pedido = $args['cod_pedido'];
    $lista = Pedido::ListarPedidosListos();
    if(empty($lista)){
      $payload = json_encode(array("error" => "no hay pedidos listos para servir"));
    }
    else{
      $cod_mesa = null;
      foreach ($lista as $pedido) {
        if($pedido["cod_pedido"] == $cod_pedido && $pedido["estado"]==Pedido::LISTO){
          $cod_mesa = $pedido["cod_mesa"];
          break;
        }
      }
      if($cod_mesa!=null){
        Pedido::ActualizarEstado($cod_pedido,Pedido::ENTREGADO);
        Mesa::ActualizarEstado($cod_mesa,Mesa::COMIENDO);
        $payload = json_encode(array("mensaje" => "Pedido entregado, cliente comiendo"));
      }
      else{
        $payload = json_encode(array("error" => "pedido no encontrado o no listo"));

      }
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function ConsultarDemora($request, $response, $args)
  {
    $parametros = $request->getQueryParams();
    $cod_pedido = $parametros["cod_pedido"];
    $cod_mesa = $parametros["cod_mesa"];
    $resultado = Pedido::verTiempo($cod_mesa,$cod_pedido);
    if(empty($resultado)){
      $payload = json_encode(array("error" => "Pedido no encontrado"));

    }else{
      if($resultado[0]["estado"] == Pedido::PREPARACION){
        $payload = json_encode(array("mensaje" => $resultado));
      }
      else{
        $payload = json_encode(array("mensaje" => "Tiempo no disponible"));
      }
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function ListarPedidosParaServir($request, $response, $args)
  {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $data = AutentificadorJWT::ObtenerData($token);
    
    $lista = Pedido::ListarPedidosListos();

    $payload = json_encode(array("lista_Pedidos (".$data->rol.")" => $lista));
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
