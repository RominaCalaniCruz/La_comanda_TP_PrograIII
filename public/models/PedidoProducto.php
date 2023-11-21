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
    public static function finalizarPedidoProducto($idProd,$cod_pedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido_producto SET estado = :estado WHERE cod_pedido=:cod AND id_producto=:idProd");
        $estado = "listo";
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idProd', $idProd, PDO::PARAM_INT);
        $consulta->bindValue(':cod', $cod_pedido, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function verEstadoGrupal($cod_pedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) AS veces_aparece FROM pedido_producto GROUP BY cod_pedido=:cod_pedido");
        $consulta->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consulta->execute();
        var_dump($consulta->fetchColumn());
        $totalProductos = $consulta->fetchColumn();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) AS veces_pendiente
        FROM pedido_producto
        WHERE pedido_producto.estado = 'en preparacion'
        GROUP BY pedido_producto.cod_pedido=:cod_pedido");
        $consulta->bindValue(':cod_pedido', $cod_pedido, PDO::PARAM_STR);
        $consulta->execute();
        var_dump($consulta->fetchColumn());
        $totalProductosIguales = $consulta->fetchColumn();
        return $totalProductosIguales==$totalProductos;
    }
    public static function traerCodxId($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT cod_pedido From pedido_producto Where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchColumn();
    }
}