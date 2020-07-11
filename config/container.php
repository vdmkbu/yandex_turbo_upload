<?php

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Twig\Environment;
use App\Service\API\TurboApi;
use App\Entity\Site\Repository\SiteRepositoryInterface;
use App\Entity\Site\Repository\SiteRepository;
use App\Entity\Counter\Repository\CounterRepositoryInterface;
use App\Entity\Counter\Repository\CounterRepository;
use App\Entity\News\Repository\NewsRepositoryInterface;
use App\Entity\News\Repository\NewsRepository;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
    },
    TurboApi::class => function(ContainerInterface $container) {
        $settings = $container->get('settings')['turbo'];

        return new TurboApi($settings['host'],
                            $settings['token'],
                            $settings['mode'] == 'prod' ? TurboApi::MODE_PRODUCTION : TurboApi::MODE_DEBUG);
    },
    NewsRepositoryInterface::class => function(ContainerInterface $container) {
        $pdo = $container->get(PDO::class);
        $twig = $container->get(Environment::class);
        return new NewsRepository($pdo, $twig);
    },
    SiteRepositoryInterface::class => function(ContainerInterface $container) {
        $pdo = $container->get(PDO::class);
        return new SiteRepository($pdo);
    },
    CounterRepositoryInterface::class => function(ContainerInterface $container) {
        return new CounterRepository();
    },
    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        return new PDO($dsn, $username, $password, $flags);
    },
    Environment::class => function (ContainerInterface $container) {
        $config = $container->get('settings')['twig'];

        $loader = new \Twig\Loader\FilesystemLoader();

        foreach ($config['template_dirs'] as $alias => $dir) {
            $loader->addPath($dir, $alias);
        }

        $environment = new Environment($loader, []);

        return $environment;
    }
];