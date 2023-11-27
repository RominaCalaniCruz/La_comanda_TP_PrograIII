<?php
//require_once require __DIR__ . '/Pedido.php';

class PedidoProducto{
    public $cod_pedido;
    public $id_producto;
    public $cantidad;
    public $id_empleado;
    public $tiempo_estimado;
    public $estado;

    public function crearPedidoProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido_producto (cod_pedido, id_producto, cantidad, estado) VALUES (:cod_pedido, :id_producto, :cantidad,:estado)");
        $consulta->bindValue(':cod_pedido', $this->cod_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function tomarPedidoProducto($id,$tiempo,$idEmp){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido_producto SET estado = :estado, tiempo_estimado=:tiempo, id_empleado=:idEmp WHERE id=:id");
        $estado = "en preparacion";
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consulta->bindValue(':idEmp', $idEmp, PDO::PARAM_INT);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function finalizarPedidoProducto($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido_producto SET estado = :estado WHERE id=:id");
        $estado = "listo para servir";
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function verEstadoGrupal($cod_pedido,$estado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consultaTotal = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) AS veces_aparece FROM pedido_producto WHERE cod_pedido =:cod_pedido GROUP BY cod_pedido");
        $consultaTotal->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consultaTotal->execute();
        $totalProductos = $consultaTotal->fetchColumn();
        var_dump($totalProductos);

        $consultaPendientes = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) AS veces_aparece
        FROM pedido_producto
        WHERE pedido_producto.estado = :estado AND pedido_producto.cod_pedido = :cod_pedido
        GROUP BY pedido_producto.cod_pedido");
        $consultaPendientes->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consultaPendientes->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consultaPendientes->execute();
        $totalPendientes = $consultaPendientes->fetchColumn();
        var_dump($totalPendientes);
        return ($totalPendientes==$totalProductos);
    }
    public static function traerCodxId($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT cod_pedido From pedido_producto Where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchColumn();
    }
    public static function CalcularImporteFinal($cod_pedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pp.cod_pedido, SUM(precio * cantidad) AS importeFinal
        FROM pedido_producto pp
        JOIN productos p ON pp.id_producto = p.id
        WHERE pp.cod_pedido = :cod_pedido
        GROUP BY pp.cod_pedido");
        $consulta->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function VerificarRolConPedidoProd($id,$rol){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pp.id , e.rol
        FROM pedido_producto pp
        JOIN empleados e ON pp.id_empleado = e.id
        WHERE e.rol = :rol AND pp.id = :id");
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}