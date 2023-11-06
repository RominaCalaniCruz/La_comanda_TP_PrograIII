<?php

class Empleado
{
    public $id;
    public $usuario;
    public $clave;
    public $estado;
    public $tipo;
    public $fecha_logueo;

    public function crearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (usuario, clave, estado, id_tipo, fecha_logueo) VALUES (:usuario, :clave, :estado, :id_tipo, :fecha_logueo)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id_tipo', $this->tipo, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_logueo', $this->fecha_logueo, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT empleados.id, empleados.usuario, empleados.clave, empleados.estado, tipos_empleados.tipo, empleados.fecha_logueo
            FROM empleados
            JOIN tipos_empleados ON empleados.id_tipo = tipos_empleados.id;");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function obtenerEmpleado($empNombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT empleados.id, empleados.usuario, empleados.clave, empleados.estado, tipos_empleados.tipo, empleados.fecha_logueo
            FROM empleados JOIN tipos_empleados ON 
            empleados.id_tipo = tipos_empleados.id WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $empNombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }

    public static function modificarEmpleado($id,$nuevoEstado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarEmpleado($usuarioID)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM empleados WHERE id = :id");
        $consulta->bindValue(':id', $usuarioID, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function tomarPedido($idEmpleado,$idPedido,$tiempo_estimado_fin,$hora_inicio)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET id_empleado = :empleado, tiempo_estimado_preparacion = :tiempo, hora_inicio = :hora WHERE id = :id");
        $consulta->bindValue(':empleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $tiempo_estimado_fin, PDO::PARAM_STR);
        $consulta->bindValue(':hora', $hora_inicio, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);

        $consulta->execute();
    }

    public static function finalizarPedido($idPedido,$hora_fin,$id_estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET hora_finalizacion = :hora_fin, id_estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $id_estado, PDO::PARAM_STR);
        $consulta->bindValue(':hora_fin', $hora_fin, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);

        $consulta->execute();
    }
    public static function listarPedidos($tipoEmpleado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, estados_pedidos.estado, pr.producto, p.fecha_pedido
            FROM pedidos p
            INNER JOIN productos pr ON p.id_producto = pr.id
            INNER JOIN tipos_empleados te ON pr.categoria = te.id
            INNER JOIN estados_pedidos ON p.id_estado = estados_pedidos.id
            where te.tipo = :tipo_empleado and estados_pedidos.estado = 'pendiente';");
        $consulta->bindValue(':tipo_empleado', $tipoEmpleado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}