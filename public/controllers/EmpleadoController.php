<?php
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
require_once __DIR__ . '/../middlewares/AutentificadorJWT.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');


class EmpleadoController extends Empleado implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        // Creamos el empleado
        $emp = new Empleado();
        $emp->usuario = $parametros['usuario'];
        $emp->clave = $parametros['clave'];
        $emp->fecha_logueo = date("Y-m-d H:i:s");
        $emp->estado = Empleado::ACTIVO;
        $emp->rol = $parametros['rol'];
        $emp->crearEmpleado();

        $payload = json_encode(array("mensaje" => "Empleado creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos empleado por usuario
        $usr = $args['usuario'];
        $usuario = Empleado::obtenerEmpleado($usr);
        if( !$usuario ){
          $payload = json_encode(["mensaje"=>"empleado no encontrado"]);
        }
        else{
          $payload = json_encode($usuario);
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Empleado::obtenerTodos();
        $payload = json_encode(array("listaEmpleados" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = new Empleado();
        // $usuario->usuario = $parametros['usuario'];
        $id = $parametros['id'];
        // $usuario->clave = $parametros['clave'];
        $estado = $parametros['estado'];
        Empleado::modificarEmpleado($id,$estado);

        $payload = json_encode(array("mensaje" => "Empleado modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $usuarioId =  $args['usuarioId'];;
        Empleado::borrarEmpleado($usuarioId);
        $payload = json_encode(array("mensaje" => "Empleado borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public static function Login($request, $response, $args){
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $empleadoLogeado = Empleado::obtenerEmpleado($usuario);
      if($empleadoLogeado->clave == $clave && $empleadoLogeado->estado == Empleado::ACTIVO){
          $token = AutentificadorJWT::CrearToken(array('usuario'=>$usuario, 'rol'=>$empleadoLogeado->rol));
          $payload = json_encode(array('token'=> $token));
      }else{
        $payload = json_encode(array('error'=> 'usuario o contraseña incorrectos'));

      }
      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
}