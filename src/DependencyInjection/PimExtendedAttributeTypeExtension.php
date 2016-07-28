<?php

namespace Pim\Bundle\ExtendedAttributeTypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * DI for the ExtendedAttributeType bundle
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class PimExtendedAttributeTypeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ .'/../Resources/config'));
        $loader->load('attribute_types.yml');
        $loader->load('form_types.yml');
        $loader->load('updaters.yml');

        $loader->load('localization/localizers.yml');
        $loader->load('localization/presenters.yml');

        $this->loadAttributeIcons($loader, $container);
        $this->loadStorageDriver($loader, $container);
    }

    /**
     * Loads the attribute icons
     *
     * @param LoaderInterface $loader
     * @param ContainerBuilder $container
     */
    protected function loadAttributeIcons(LoaderInterface $loader, ContainerBuilder $container)
    {
        $loader->load('attribute_icons.yml');

        $icons = $container->getParameter('pim_enrich.attribute_icons');
        $icons += $container->getParameter('pim_extended_attribute_type.attribute_icons');
        if ($container->hasParameter('pimee_enrich.attribute_icons')) {
            $icons += $container->getParameter('pimee_enrich.attribute_icons');
        }
        $container->setParameter('pim_enrich.attribute_icons', $icons);
    }

    /**
     * Loads the DI depending on the storage driver
     *
     * @param LoaderInterface $loader
     * @param ContainerBuilder $container
     */
    protected function loadStorageDriver(LoaderInterface $loader, ContainerBuilder $container)
    {
        $storageDriver = $container->getParameter('pim_catalog_product_storage_driver');
        $storageConfig = sprintf('storage_driver/%s.yml', $storageDriver);

        if (file_exists(__DIR__ . '/../Resources/config/' . $storageConfig)) {
            $loader->load($storageConfig);
        }
    }
}
