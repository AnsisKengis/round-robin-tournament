<?php

namespace App\Config;

use Doctrine\ORM\EntityManager;

class DatabaseConfig
{
    private static ?EntityManager $entityManager = null;

    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager === null) {
            $dependencyFactory = require __DIR__ . '/../../cli-config.php';
            self::$entityManager = $dependencyFactory->getEntityManager();
        }
        return self::$entityManager;
    }
}