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
    public $foto_mesa;
    public $estado;
    public $mozo;
    public $nombre_cliente;
    public $fecha_pedido;
    public $tiempo_estimado_preparacion;
    public $hora_inicio;
    public $hora_finalizacion;
    public $importe_total;
    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (cod_pedido, cod_mesa, estado, id_mozo, nombre_cliente, fecha_pedido) VALUES (:cod_pedido, :cod_mesa, :estado, :id_mozo, :nombre_cliente, :fecha_pedido)");
        $consulta->bindValue(':cod_pedido', $this->cod_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':cod_mesa', $this->cod_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', Pedido::PENDIENTE, PDO::PARAM_STR);
        $consulta->bindValue(':id_mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_pedido', $this->fecha_pedido,PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {//arreglar
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedidos.id, pedidos.cod_pedido, pedidos.cod_mesa, pedidos.estado, empleados.usuario as mozo, pedidos.nombre_cliente, pedidos.fecha_pedido
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

    public static function finalizarPedido($codPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET hora_finalizacion = :hora_fin, estado = :estado WHERE cod_pedido = :cod_pedido ");
        $consulta->bindValue(':estado', Pedido::LISTO, PDO::PARAM_STR);
        $consulta->bindValue(':hora_fin', date('H:i:s'), PDO::PARAM_STR);
        $consulta->bindValue(':id', $codPedido, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function listarPendientes($tipoEmpleado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pp.id, pp.cod_pedido, pr.producto, pp.cantidad
            FROM pedido_producto pp
            INNER JOIN productos pr ON pp.id_producto = pr.id
            INNER JOIN empleados e ON pr.categoria = e.rol
            where e.rol = :tipo_empleado and pp.estado = :estado;");
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

    public static function asignarTiempoEstimado($cod_pedido,$tiempo){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET tiempo_estimado_finalizacion = :tiempo WHERE cod_pedido = :cod_pedido");
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consulta->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function actualizarEstado($cod_pedido,$estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET estado = :estado WHERE cod_pedido = :codigo");
        $consulta->bindValue(":estado",$estado,PDO::PARAM_STR);
        $consulta->bindValue(":codigo",$cod_pedido,PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function actualizarPedido($cod_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET tiempo_estimado_preparacion = (SELECT MAX(tiempo_estimado) FROM pedido_producto), hora_inicio = :ahora WHERE cod_pedido = :codigo");
        $consulta->bindValue(":codigo",$cod_pedido,PDO::PARAM_STR);
        $consulta->bindValue(":ahora",date("H:i:s"), PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function actualizarHoraInicio($cod_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET hora_inicio = :tiempo WHERE cod_pedido = :codigo");
        $consulta->bindValue(":codigo",$cod_pedido,PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function cargarFoto($cod_pedido,$foto){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET foto_mesa = :foto WHERE cod_pedido = :cod_pedido");
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consulta->execute();
    }

}