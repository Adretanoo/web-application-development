<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../src/config.php';

use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection($config['database']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

session_start();

$container = new Container();
AppFactory::setContainer($container);

$twigInstance = Twig::create($config['twig']['path'], ['cache' => false, 'debug' => true]);

$container->set('view', $twigInstance);
$container->set(\Slim\Views\Twig::class, $twigInstance);

$twigInstance->getEnvironment()->addGlobal('session', $_SESSION);
$app = AppFactory::create();

$app->add(TwigMiddleware::createFromContainer($app, 'view'));
$app->add(new MethodOverrideMiddleware());

require __DIR__ . '/../src/routes.php';

$app->run();