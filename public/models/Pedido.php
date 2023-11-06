<?php

class Pedido{
    public $id;
    public $cod_pedido;
    public $cod_mesa;
    public $estado;
    public $mozo;
    public $empleado;
    public $nombre_cliente;
    public $producto;
    public $fecha_pedido;
    public $tiempo_estimado_preparacion;
    public $hora_inicio;
    public $hora_finalizacion;
    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (cod_pedido, cod_mesa, id_estado, id_mozo, nombre_cliente, id_producto, fecha_pedido) VALUES (:cod_pedido, :cod_mesa, :id_estado, :id_mozo, :nombre_cliente, :id_producto, :fecha_pedido)");
        $consulta->bindValue(':cod_pedido', $this->cod_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':cod_mesa', $this->cod_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':id_estado', $this->estado, PDO::PARAM_INT);
        $consulta->bindValue(':id_mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $this->producto, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_pedido', $this->fecha_pedido,PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedidos.id, pedidos.cod_pedido, pedidos.cod_mesa, estados_pedidos.estado, empleados.usuario as mozo, pedidos.nombre_cliente, productos.producto, pedidos.fecha_pedido
            FROM pedidos
            JOIN estados_pedidos ON pedidos.id_estado = estados_pedidos.id
            JOIN empleados ON pedidos.id_mozo = empleados.id
            JOIN productos ON pedidos.id_producto = productos.id");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }


}