<?php
namespace Snoke\OAuth\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
class ConfigurationPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $configFile = $container->getParameter('kernel.project_dir') . '/config/packages/snoke_oauth.yaml';

        $bundleConfigFile = __DIR__ . '/../../Resources/config/snoke_oauth.yaml';

        if (!file_exists($configFile)) {
            $defaultConfig = file_get_contents($bundleConfigFile);
            file_put_contents($configFile, $defaultConfig);
        }
    }
}
