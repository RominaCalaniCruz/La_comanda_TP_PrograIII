<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once __DIR__ . '/../models/Roles.php';

class RolesMW
{
    public static function EsSocioMW($request, $handler)
    {
        // $parametros = $request->getParsedBody();
        // $sector = $parametros['sector'];
        // if($sector == Roles::SOCIO){
        //     $response = $handler->handle($request);
        // }
        // else{
        //     $response = new Response();
        //     $payload = json_encode(['mensaje'=>'no eres socio']);
        //     $response->getBody()->write($payload);
        // }
        return self::VerificarTipoRol($request,$handler,Roles::SOCIO,"No eres socio");
    }
    public static function EsMozoMW($request, $handler)
    {
        // $parametros = $request->getParsedBody();
        // $sector = $parametros['sector'];
        // if($sector == Roles::MOZO){
        //     $response = $handler->handle($request);
        // }
        // else{
        //     $response = new Response();
        //     $payload = json_encode(['mensaje'=>'no eres mozo']);
        //     $response->getBody()->write($payload);
        // }
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
        $parametros = $request->getParsedBody();
        $sector = $parametros['rol'];

        if ($sector == $rol) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $payload = json_encode(['mensaje' => $mensajeError]);
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}