<?php

class Pedido{
    const PENDIENTE = "pendiente";
    const PREPARACION = "en preparacion";
    const LISTO = "listo para servir";
    const ENTREGADO = "entregado";
    const CANCELADO = "cancelado";
    const FINALIZADO = "finalizado";
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
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedidos.cod_pedido, pedidos.cod_mesa, pedidos.estado, empleados.usuario as mozo, pedidos.nombre_cliente, pedidos.fecha_pedido, pedidos.tiempo_estimado_preparacion
            FROM pedidos
            JOIN empleados ON pedidos.id_mozo = empleados.id");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function obtenerUno($cod_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pedidos.cod_pedido, pedidos.cod_mesa, pedidos.estado, pedidos.id_mozo as mozo, pedidos.nombre_cliente, 
            pedidos.fecha_pedido
            FROM pedidos            
            WHERE pedidos.cod_pedido = :codigo");//JOIN empleados ON pedidos.id_mozo = empleados.id
        $consulta->bindValue(":codigo",$cod_pedido,PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function actualizarPedidoListo($codPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET hora_finalizacion = :hora_fin, estado = :estado WHERE cod_pedido = :cod_pedido ");
        $consulta->bindValue(':estado', Pedido::LISTO, PDO::PARAM_STR);
        $consulta->bindValue(':hora_fin', date('H:i:s'), PDO::PARAM_STR);
        $consulta->bindValue(':cod_pedido', $codPedido, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function ListarXEstadoYRol($tipoEmpleado,$estado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT pp.id, pp.cod_pedido, pr.producto, pp.cantidad
            FROM pedido_producto pp
            INNER JOIN productos pr ON pp.id_producto = pr.id
            INNER JOIN empleados e ON pr.categoria = e.rol
            where e.rol = :tipo_empleado and pp.estado = :estado;");
        $consulta->bindValue(':tipo_empleado', $tipoEmpleado, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function ListarPedidosListos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.cod_pedido, p.cod_mesa, p.estado
            FROM pedidos p
            WHERE p.estado = :estado");
        $consulta->bindValue(':estado', self::LISTO, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function ActualizarEstado($cod_pedido,$estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET estado = :estado WHERE cod_pedido = :codigo");
        $consulta->bindValue(":estado",$estado,PDO::PARAM_STR);
        $consulta->bindValue(":codigo",$cod_pedido,PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function ActualizarTiempoEstimado($cod_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET tiempo_estimado_preparacion = (SELECT MAX(tiempo_estimado) FROM pedido_producto WHERE cod_pedido = :codigoPedido), hora_inicio = :ahora WHERE cod_pedido = :codigo");
        $consulta->bindValue(":codigoPedido",$cod_pedido,PDO::PARAM_STR);  
        $consulta->bindValue(":codigo",$cod_pedido,PDO::PARAM_STR);        
        $consulta->bindValue(":ahora",date("H:i:s"), PDO::PARAM_STR);
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
    public static function ActualizarImporteAPagar($cod_pedido,$importe){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedidos SET importe_total = :total WHERE cod_pedido = :cod_pedido");
        $consulta->bindValue(':total', $importe, PDO::PARAM_STR);
        $consulta->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function verTiempo($cod_mesa,$cod_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT cod_pedido,cod_mesa,tiempo_estimado_preparacion,estado FROM pedidos WHERE cod_mesa = :cod_mesa AND cod_pedido = :cod_pedido");
        $consulta->bindValue(":cod_mesa",$cod_mesa,PDO::PARAM_STR);
        $consulta->bindValue(":cod_pedido",$cod_pedido,PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}