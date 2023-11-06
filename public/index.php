<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/EmpleadoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';
require_once './db/AccesoDatos.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
// $app->setBasePath('/public');
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("hola alumnos de los lunes!");
    return $response;
});

// peticiones
$app->group('/empleados', function (RouteCollectorProxy $group) {
    $group->get('[/]', \EmpleadoController::class . ':TraerTodos');
    $group->get('/{usuario}', \EmpleadoController::class . ':TraerUno');
    $group->post('[/]', \EmpleadoController::class . ':CargarUno');
    $group->post('/modificar', \EmpleadoController::class . ':ModificarUno');
    $group->delete('/{usuarioId}', \EmpleadoController::class . ':BorrarUno');
    $group->get('/listar_pedidos/{tipo}', \EmpleadoController::class .':ListarPedidosPendientes');
  });

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  // $group->get('/{usuario}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  // $group->post('/modificar', \MesaController::class . ':ModificarUno');
  // $group->delete('/{usuarioId}', \MesaController::class . ':BorrarUno');
});
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  // $group->get('/{usuario}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  // $group->post('/modificar', \ProductoController::class . ':ModificarUno');
  // $group->delete('/{usuarioId}', \ProductoController::class . ':BorrarUno');
});
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  // $group->get('/{usuario}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \PedidoController::class . ':CargarUno');
  // $group->post('/modificar', \MesaController::class . ':ModificarUno');
  // $group->delete('/{usuarioId}', \MesaController::class . ':BorrarUno');
});

// Run app
$app->run();

