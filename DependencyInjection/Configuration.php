<?php

namespace Emmedy\H5PBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    const H5P_VERSION = '0.1'; // version of Symfony H5P bundle

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('emmedy_h5p');

        $rootNode
            ->children()
                ->scalarNode('storage_dir')->defaultValue("h5p")->end()
                ->scalarNode('web_dir')->defaultValue("web")->end()
                ->booleanNode('hub_is_enabled')->defaultTrue()->end()
                ->booleanNode('send_usage_statistics')->defaultTrue()->end()
                ->booleanNode('save_content_state')->defaultFalse()->end()
                ->integerNode('save_content_fequency')->defaultValue(30)->end()
                ->booleanNode('hub_is_enabled')->defaultTrue()->end()
                ->scalarNode('whitelist')->defaultValue(\H5PCore::$defaultContentWhitelist)->end()
                ->scalarNode('library_whitelist_extras')->defaultValue(\H5PCore::$defaultLibraryWhitelistExtras)->end()
                ->booleanNode('dev_mode')->defaultFalse()->end()
                ->booleanNode('first_runnable_saved')->defaultFalse()->end()
                ->scalarNode('site_type')->defaultValue('local')->end()
                ->scalarNode('site_uuid')->defaultValue('')->end()
                ->booleanNode('send_usage_statistics')->defaultTrue()->end()
                ->booleanNode(\H5PCore::DISPLAY_OPTION_ABOUT)->defaultTrue()->end()
                ->booleanNode(\H5PCore::DISPLAY_OPTION_FRAME)->defaultTrue()->end()
                ->integerNode(\H5PCore::DISPLAY_OPTION_DOWNLOAD)->defaultValue(\H5PDisplayOptionBehaviour::NEVER_SHOW)->end()
                ->integerNode(\H5PCore::DISPLAY_OPTION_EMBED)->defaultValue(\H5PDisplayOptionBehaviour::NEVER_SHOW)->end()
                ->booleanNode(\H5PCore::DISPLAY_OPTION_COPYRIGHT)->defaultTrue()->end()
                ->integerNode('content_type_cache_updated_at')->defaultValue(0)->end()
                ->booleanNode('enable_lrs_content_types')->defaultFalse()->end()
                ->booleanNode('use_permission')->defaultFalse()->end()
                ->scalarNode('entity_manager')
                    ->info('Name of the entity manager that you wish to use for managing clients and tokens.')
                    ->cannotBeEmpty()
                    ->defaultValue('default')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
