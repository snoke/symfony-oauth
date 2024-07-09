<?php
namespace Snoke\OAuth\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
class ConfigurationPass implements CompilerPassInterface
{
    private function createRoutesFile($container) {
        $configFile = $container->getParameter('kernel.project_dir') . '/config/routes/snoke_oauth.yaml';

        $bundleConfigFile = __DIR__ . '/../../Resources/config/routes.yaml';

        if (!file_exists($configFile)) {
            $defaultConfig = file_get_contents($bundleConfigFile);
            file_put_contents($configFile, $defaultConfig);
        }
    }

    private function createPackageFile($container) {
        $configFile = $container->getParameter('kernel.project_dir') . '/config/packages/snoke_o_auth.yaml';

        $bundleConfigFile = __DIR__ . '/../../Resources/config/snoke_o_auth.yaml';

        if (!file_exists($configFile)) {
            $defaultConfig = file_get_contents($bundleConfigFile);
            file_put_contents($configFile, $defaultConfig);
        }
    }

    public function process(ContainerBuilder $container)
    {
        $this->createRoutesFile($container);
        $this->createPackageFile($container);
    }
}
