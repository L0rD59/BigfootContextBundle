<?php

namespace Bigfoot\Bundle\ContextBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BigfootContextExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $contextConfig = $config['contexts'];
        if (isset($contextConfig['language'])) {
            $contextConfig['language']['values'] = $container->getParameter('bigfoot_core.languages.front');
        }

        $container->setParameter('bigfoot_contexts', $contextConfig);

        $container->setParameter('bigfoot_context_entities', $config['entities']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
