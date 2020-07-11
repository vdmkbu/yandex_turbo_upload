<?php
error_reporting(0);
ini_set('display_errors', '0');

$settings = [];
$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';

$settings['error'] = [

    // Should be set to false in production
    'display_error_details' => true,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];

$settings['db'] = [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST'),
    'username' => getenv('DB_USER'),
    'database' => getenv('DB_NAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'flags' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Set character set
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
    ],
];

$settings['twig'] = [
  'template_dirs' => [
      \Twig\Loader\FilesystemLoader::MAIN_NAMESPACE => __DIR__ . '/../templates'
  ]
];

$settings['turbo'] = [
   'host' => getenv('TURBO_API_HOST'),
   'token' => getenv('TURBO_API_TOKEN'),
   'mode' => getenv('TURBO_API_MODE')
];
return $settings;