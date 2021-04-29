<?php

namespace Emmedy\H5PBundle\DependencyInjection;

use Emmedy\H5PBundle\Core\H5PIntegration;
use Emmedy\H5PBundle\Core\H5POptions;
use Emmedy\H5PBundle\Core\H5PSymfony;
use Emmedy\H5PBundle\Editor\EditorAjax;
use Emmedy\H5PBundle\Editor\EditorStorage;
use Emmedy\H5PBundle\Editor\LibraryStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class EmmedyH5PExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->configureEntityManager($container, $config['entity_manager']);

        $definition = $container->getDefinition("emmedy_h5p.core");
        $definition->replaceArgument(1, $container->getParameter('kernel.project_dir') . '/' . $config['web_dir'] . '/' . $config["storage_dir"]);
        $definition->replaceArgument(2, '/');

        $definition = $container->getDefinition("emmedy_h5p.options");
        $definition->replaceArgument(0, $config);
    }

    private function configureEntityManager(ContainerBuilder $container, string $entityManagerName): void
    {
        $entityManager = new Reference(
            sprintf('doctrine.orm.%s_entity_manager', $entityManagerName)
        );

        $container
            ->getDefinition('emmedy_h5p.options')
            ->replaceArgument(2, $entityManager)
        ;

        $container
            ->getDefinition('emmedy_h5p.editor_storage')
            ->replaceArgument(3, $entityManager)
        ;

        $container
            ->getDefinition('emmedy_h5p.interface')
            ->replaceArgument(3, $entityManager)
        ;

        $container
            ->getDefinition('emmedy_h5p.editor_ajax')
            ->replaceArgument(0, $entityManager)
        ;

        $container
            ->getDefinition('emmedy_h5p.integration')
            ->replaceArgument(3, $entityManager)
        ;

        $container
            ->getDefinition('emmedy_h5p.library_storage')
            ->replaceArgument(2, $entityManager)
        ;
    }
}
