<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './models/PedidoProducto.php';
require_once './middlewares/AutentificadorJWT.php';

class Autentificador{
    public static function ValidarSector($request, $handler){
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $parametros = $request->getParsedBody();
        $idPedidoProd = $parametros["id_pedidoProd"];
        try {
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            $resultado = PedidoProducto::VerificarRolConPedidoProd($idPedidoProd,$data->rol);
            if(!empty($resultado))
            {
                $request->datosToken = $data;
                $response = $handler->handle($request);
            }
            else{
                throw new Exception();
            }
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(['mensaje' => "este no es tu sector"]);
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}