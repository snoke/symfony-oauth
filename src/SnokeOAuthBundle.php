<?php
namespace Snoke\OAuth;

use Snoke\OAuth\DependencyInjection\Compiler\ConfigurationPass;
use Snoke\OAuth\DependencyInjection\Compiler\UninstallPass;
use Snoke\OAuth\DependencyInjection\SnokeOAuthExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
class SnokeOAuthBundle extends Bundle
{

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new DependencyInjection\SnokeOAuthExtension();
        }

        return $this->extension;
    }
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new ConfigurationPass());
        $container->addCompilerPass(new UninstallPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->prependExtensionConfig('twig', [
            'paths' => [
                __DIR__ . '/templates' => 'snoke_oauth',
            ],
        ]);
        $container->registerExtension(new SnokeOAuthExtension());
    }
}