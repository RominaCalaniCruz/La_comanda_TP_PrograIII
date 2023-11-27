<?php
require_once './models/Encuesta.php';
class EncuestaController
{
    public function Cargar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $cod_mesa = $parametros['cod_mesa'];
        $cod_pedido = $parametros['cod_pedido'];
        $mesa_puntos = $parametros['mesa_puntos'];
        $local_puntos = $parametros['local_puntos'];
        $mozo_puntos = $parametros['mozo_puntos'];
        $emp_puntos = $parametros['emp_puntos'];
        $experiencia = $parametros['experiencia'];
        $encuesta = new Encuesta($cod_mesa,$cod_pedido,$mesa_puntos,$local_puntos,$mozo_puntos,$emp_puntos,$experiencia);
        $encuesta->Guardar();

        $payload = json_encode(array("mensaje" => "Encuesta enviada"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}