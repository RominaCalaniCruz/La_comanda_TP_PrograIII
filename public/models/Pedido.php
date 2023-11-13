<?php

class Pedido{
    const PENDIENTE = "pendiente";
    const PREPARACION = "en preparacion";
    const LISTO = "listo para servir";
    const ENTREGADO = "entregado";
    const CANCELADO = "cancelado";
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
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (cod_pedido, cod_mesa, estado, id_mozo, nombre_cliente, id_producto, fecha_pedido) VALUES (:cod_pedido, :cod_mesa, :estado, :id_mozo, :nombre_cliente, :id_producto, :fecha_pedido)");
        $consulta->bindValue(':cod_pedido', $this->cod_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':cod_mesa', $this->cod_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
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
            "SELECT pedidos.id, pedidos.cod_pedido, pedidos.cod_mesa, pedidos.estado, empleados.usuario as mozo, pedidos.nombre_cliente, productos.producto, pedidos.fecha_pedido
            FROM pedidos
            JOIN estados_pedidos ON pedidos.id_estado = estados_pedidos.id
            JOIN empleados ON pedidos.id_mozo = empleados.id
            JOIN productos ON pedidos.id_producto = productos.id");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
    public static function obtenerUno($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedidos.id, pedidos.cod_pedido, pedidos.cod_mesa, pedidos.estado, pedidos.id_mozo as mozo, pedidos.id_empleado as empleado, pedidos.nombre_cliente, 
            pedidos.fecha_pedido, pedidos.id_producto as producto, pedidos.hora_inicio, pedidos.hora_finalizacion, pedidos.tiempo_estimado_preparacion
            FROM pedidos
            
            WHERE id = :id");//JOIN empleados ON pedidos.id_mozo = empleados.id
        $consulta->bindValue(":id",$id,PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }


    public static function comenzar($idEmpleado,$idPedido,$tiempo_estimado_fin)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, id_empleado = :empleado, tiempo_estimado_preparacion = :tiempo, hora_inicio = :hora WHERE id = :id");
        $consulta->bindValue(':empleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $tiempo_estimado_fin, PDO::PARAM_STR);
        $consulta->bindValue(':hora', date('H:i:s'), PDO::PARAM_STR);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':estado', Pedido::PREPARACION, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function finalizarPedido($idPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET hora_finalizacion = :hora_fin, estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', Pedido::LISTO, PDO::PARAM_STR);
        $consulta->bindValue(':hora_fin', date('H:i:s'), PDO::PARAM_STR);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function listarPendientes($tipoEmpleado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, p.estado, pr.producto, p.fecha_pedido
            FROM pedidos p
            INNER JOIN productos pr ON p.id_producto = pr.id
            INNER JOIN empleados e ON pr.categoria = e.rol
            where e.rol = :tipo_empleado and p.estado = :estado;");
        $consulta->bindValue(':tipo_empleado', $tipoEmpleado, PDO::PARAM_STR);
        $consulta->bindValue(':estado', Pedido::PENDIENTE, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function traerCodPedido($cod_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT cod_pedido FROM pedidos WHERE cod_mesa = :codigo");
        $consulta->bindValue(":codigo",$cod_mesa,PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchColumn();
    }
}