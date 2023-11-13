<?php

class Producto{
    public $id;
    public $producto;
    public $descripcion;
    public $precio;
    public $categoria;
    
    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (producto, descripcion, precio, categoria) VALUES (:producto, :descripcion, :precio, :categoria)");
        $consulta->bindValue(':producto', $this->producto, PDO::PARAM_STR);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        // $consulta->bindValue(':tiempo_preparacion', $this->tiempo_preparacion, PDO::PARAM_STR);
        $consulta->bindValue(':categoria', $this->categoria, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT id, producto, descripcion, precio, categoria FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }
    public static function traerCategoriaProd($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT productos.categoria FROM productos WHERE id = :id");
        $consulta->bindValue(":id",$id,PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchColumn();
    }

}