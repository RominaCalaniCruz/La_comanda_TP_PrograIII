<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once __DIR__ . '/../models/Roles.php';
require_once __DIR__ .'/AutentificadorJWT.php';

class RolesMW
{
    public static function EsSocioMW($request, $handler)
    {
        return self::VerificarTipoRol($request,$handler,Roles::SOCIO,"No eres socio");
    }
    public static function EsMozoMW($request, $handler)
    {
        return self::VerificarTipoRol($request,$handler,Roles::MOZO,"No eres mozo");
    }
    public static function EsCocineroMW($request, $handler){
        return self::VerificarTipoRol($request,$handler,Roles::COCINERO,"No eres cocinero");
    }
    public static function EsCerveceroMW($request, $handler){
        return self::VerificarTipoRol($request,$handler,Roles::CERVECERO,"No eres cervecero");
    }
    public static function EsBartenderMW($request, $handler){
        return self::VerificarTipoRol($request,$handler,Roles::BARTENDER,"No eres bartender");
    }
    public static function EsDelCandyBar($request, $handler){
        return self::VerificarTipoRol($request,$handler,Roles::CANDYBAR,"No eres del area de candy bar");
    }

    public static function VerificarTipoRol($request, $handler, $rol, $mensajeError)
    {
        try {
            
            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            if($data->rol == $rol)
            {
                $request->datosToken = $data;
                $response = $handler->handle($request);
            }
            else{
                throw new Exception();
            }
        } catch (Exception $e) {
            // $data = AutentificadorJWT::ObtenerData($token);
            $response = new Response();
            $payload = json_encode(['mensaje' => $mensajeError]);
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');

    }
}