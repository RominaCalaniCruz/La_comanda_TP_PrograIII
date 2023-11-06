<?php

require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto{

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        // Creamos el producto
        $prod = new Producto();
        $prod->producto = $parametros['producto'];
        $prod->descripcion = $parametros['descripcion'];
        $prod->precio = $parametros['precio'];
        $prod->tiempo_preparacion = $parametros['tiempo_preparacion'];;
        $prod->categoria = $parametros['categoria'];
        $prod->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}