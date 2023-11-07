<?php

use Slim\Handlers\Strategies\RequestHandler;

class Logger
{
    public static function LogOperacion($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }
}