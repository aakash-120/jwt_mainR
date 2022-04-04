<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;
use App\Component\Locale;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;

require("../vendor/autoload.php");
$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
        APP_PATH . "/listeners/",
        APP_PATH . "/component/",
    ]
);


//this should be before loader->register
$loader->registerNamespaces(
    [
        'App\Listeners' => APP_PATH . '/listeners',
        'App\Component' => APP_PATH . '/component'
    ]
);

$loader->register();

$container = new FactoryDefault();



$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);



$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);


$application = new Application($container);





$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'store',
            ]
        );
    }
);


$container->set('locale', (new Locale())->getTranslator());
// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );

$application = new Application($container);
$eventManager = new EventsManager();
$eventManager->attach(
    'notifications',
    new App\Listeners\notificationListeners()
);
$eventManager->attach(
    'application:beforeHandleRequest',
    new App\Listeners\notificationListeners()
);
$container->set(
    'eventManager',
    $eventManager
);

$application->seteventsManager($eventManager);       //ye code build acl se phle comment krna hai

// $eventManager->attach(
//     'db:afterQuery',
//     function (Event $event, $connection) use ($logger)
//     {
//         $logger->error($connection->getSQLStatement());
//     }
// );

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
