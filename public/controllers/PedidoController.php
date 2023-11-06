<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
class PedidoController extends Pedido {

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->cod_pedido = $parametros['cod_pedido'];
        $pedido->cod_mesa = $parametros['cod_mesa'];
        $pedido->fecha_pedido = date("Y-m-d H:i:s");
        $pedido->estado = 1; //1 = Pendiente
        $pedido->mozo = $parametros['id_mozo'];
        $pedido->nombre_cliente = $parametros['nombre_cliente'];
        $pedido->producto = $parametros['id_producto'];
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

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

}