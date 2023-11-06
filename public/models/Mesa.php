<?php

class Mesa{
    public $cod_mesa;
    public $foto;
    public $estado;
    public function CrearMesa(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        try{
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (cod_mesa, foto, id_estado) VALUES (:cod_mesa, :foto, :id_estado)");
            $consulta->bindValue(':cod_mesa', $this->cod_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
            $consulta->bindValue(':id_estado', $this->estado, PDO::PARAM_INT);
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
            "SELECT mesas.cod_mesa, mesas.foto, estados_mesas.estado
            FROM mesas
            JOIN estados_mesas ON mesas.id_estado = estados_mesas.id;");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
}