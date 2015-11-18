<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function boot()
    {
        $rootDir = $this->container->getParameter('kernel.root_dir');
        $env = $this->container->getParameter('kernel.environment');

        $dataEnvDir = sprintf(
            '%s/Resources/data/%s',
            $rootDir,
            $env
        );

        if (!file_exists($dataEnvDir)) {
            mkdir($dataEnvDir);
        }
    }
}
