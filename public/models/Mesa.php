<?php

class Mesa{
    const ESPERANDO = "cliente esperando pedido";
    const COMIENDO = "cliente comiendo";
    const PAGANDO = "cliente pagando";
    const CERRADA = "cerrada";
    public $cod_mesa;
    public $estado;
    public function CrearMesa(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        try{
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (cod_mesa, estado) VALUES (:cod_mesa, :estado)");
            $consulta->bindValue(':cod_mesa', $this->cod_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
            $consulta->execute();
            $salida = json_encode(array("mensaje" => "Mesa creada con exito"));
        }
        catch(PDOException $e){
            // if($e->errorInfo[1] == '1062'){
            // }
            $salida = $e->getMessage();
        }
        // return $objAccesoDatos->obtenerUltimoId();
        return $salida;
    }
    public static function ListarMesas(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT mesas.cod_mesa, mesas.estado
            FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
    public static function ListarUna($cod_mesa){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT mesas.cod_mesa, mesas.estado
            FROM mesas WHERE cod_mesa = :codigo");
        $consulta->bindValue(":codigo",$cod_mesa,PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Mesa');
    }
    public static function ActualizarEstado($cod_mesa,$estado){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE mesas SET estado = :estado WHERE cod_mesa = :codigo");
        $consulta->bindValue(":estado",$estado,PDO::PARAM_STR);
        $consulta->bindValue(":codigo",$cod_mesa,PDO::PARAM_STR);
        $consulta->execute();
    }
    
    public static function MasUsada(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT m.cod_mesa, COUNT(p.cod_pedido) AS cantidad_pedidos
            FROM mesas m
            JOIN pedidos p ON m.cod_mesa = p.cod_mesa
            GROUP BY m.cod_mesa
            ORDER BY cantidad_pedidos DESC
            LIMIT 1");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}