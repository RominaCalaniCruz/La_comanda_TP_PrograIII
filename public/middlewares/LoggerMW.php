<?php

use Slim\Handlers\Strategies\RequestHandler;

class LoggerMW
{
    public static function Login($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }
}