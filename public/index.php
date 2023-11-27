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
require_once './controllers/EncuestaController.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/RolesMW.php';
require_once './middlewares/AutentificadorMW.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
// $app->setBasePath('/public');
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args)
{
  $response->getBody()->write("TP La comanda Progra 3 Romina Calani");
  return $response;
});

$app->group('/empleados', function (RouteCollectorProxy $group)
{
  $group->get('[/]', \EmpleadoController::class . ':TraerTodos');
  $group->get('/csv', \EmpleadoController::class . ':DescargarCSV');
  $group->post('/cargarCSV', \EmpleadoController::class . ':CargarCSV');
  $group->get('/{usuario}', \EmpleadoController::class . ':TraerUno');
  $group->post('[/]', \EmpleadoController::class . ':CargarUno');
  $group->put('[/]', \EmpleadoController::class . ':ModificarUno');
  $group->delete('/{usuarioId}', \EmpleadoController::class . ':BorrarUno');
})->add(\RolesMW::class . '::EsSocioMW');

$app->group('/mesas', function (RouteCollectorProxy $group)
{
  $group->get('/mas_usada', \MesaController::class . ':MesaMasUsada');
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->post('/cerrar', \MesaController::class . ':CerrarMesa');
})->add(\RolesMW::class . '::EsSocioMW');

$app->group('/productos', function (RouteCollectorProxy $group)
{
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group)
{
  $group->get('/listar/para_servir', \PedidoController::class . ':ListarPedidosParaServir')->add(\RolesMW::class . '::EsMozoMW');
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\RolesMW::class . '::EsSocioMW');
  $group->post('[/]', \PedidoController::class . ':CrearUno')->add(\RolesMW::class . '::EsMozoMW');
  $group->post('/subir_foto', \PedidoController::class . ':SubirFotoMesa')->add(\RolesMW::class . '::EsMozoMW');
  $group->post('/calcular', \PedidoController::class . ':CalcularImporte')->add(\RolesMW::class . '::EsMozoMW');
  $group->get('/entregar/{cod_pedido}', \PedidoController::class . ':Entregar')->add(\RolesMW::class . '::EsMozoMW');
  $group->get('/listar', \PedidoController::class . ':ListarPedidosPendientes');
  $group->post('/iniciar', \PedidoController::class . ':IniciarUnPedido');
  $group->post('/terminar', \PedidoController::class . ':TerminarUnPedido')->add(\Autentificador::class . '::ValidarSector');
});


$app->post('/encuesta', \EncuestaController::class . ':Cargar');
$app->get('/consultar', \PedidoController::class . ':ConsultarDemora');

$app->post('/login', \EmpleadoController::class . ':Login');

// Run app
$app->run();

