<?php

namespace App\Controllers;

use Doctrine\ORM\EntityManager;

abstract class BaseController
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function render(string $view, array $data = []): string
    {
        extract($data);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View '$view' not found.");
        }

        ob_start();
        include $viewFile;
        return ob_get_clean();
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}