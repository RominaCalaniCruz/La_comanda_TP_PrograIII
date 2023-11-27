<?php


class Encuesta{
    public $id;
    public $cod_mesa;
    public $cod_pedido;
    public $mesa_puntos;
    public $local_puntos;
    public $mozo_puntos;
    public $emp_puntos;
    public $experiencia;

    public function __construct($cod_mesa,$cod_pedido,$mesa_puntos,$local_puntos,$mozo_puntos,$emp_puntos,$experiencia){
        $this->cod_mesa = $cod_mesa;
        $this->cod_pedido = $cod_pedido;
        $this->mesa_puntos = $mesa_puntos;
        $this->local_puntos = $local_puntos;
        $this->mozo_puntos = $mozo_puntos;
        $this->emp_puntos = $emp_puntos;
        $this->experiencia = $experiencia;
    }
    public function Guardar(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (cod_mesa, cod_pedido, mesa_puntos, local_puntos, mozo_puntos, emp_puntos, experiencia) VALUES (:cod_mesa, :cod_pedido, :mesa_puntos, :local_puntos, :mozo_puntos, :emp_puntos, :experiencia)");
        $consulta->bindValue(':cod_mesa', $this->cod_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':cod_pedido', $this->cod_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':mesa_puntos', $this->mesa_puntos, PDO::PARAM_INT);
        $consulta->bindValue(':local_puntos', $this->local_puntos, PDO::PARAM_INT);
        $consulta->bindValue(':mozo_puntos', $this->mozo_puntos, PDO::PARAM_INT);
        $consulta->bindValue(':emp_puntos', $this->emp_puntos, PDO::PARAM_INT);
        $consulta->bindValue(':experiencia', $this->experiencia, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }
}