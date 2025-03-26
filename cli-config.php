<?php

require 'vendor/autoload.php';

use Doctrine\Migrations\DependencyFactory;
use Dotenv\Dotenv;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = new PhpFile('migrations.php');

$params = [
    'host'     => $_ENV['DB_HOST'] ?? 'localhost',
    'user'     => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'dbname'   => $_ENV['DB_NAME'] ?? 'tournament_db',
    'driver'   => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
];

$ormConfig = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/src/Entities'],
    isDevMode: true
);

$connection = DriverManager::getConnection($params, $ormConfig);
$entityManager = new EntityManager($connection, $ormConfig);

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));