<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->alias('hateoas.configuration.provider', 'hateoas.configuration.provider.chain')
        ->public();

    $services->set('hateoas.configuration.provider.chain', \Hateoas\Configuration\Provider\ChainProvider::class)
        ->private()
        ->args([[]]);

    $services->set('hateoas.configuration.provider.function', \Hateoas\Configuration\Provider\FunctionProvider::class)
        ->private()
        ->tag('hateoas.relation_provider');

    $services->set('hateoas.configuration.provider.static_method', \Hateoas\Configuration\Provider\StaticMethodProvider::class)
        ->private()
        ->tag('hateoas.relation_provider');

    $services->set('hateoas.configuration.provider.expression', \Hateoas\Configuration\Provider\ExpressionEvaluatorProvider::class)
        ->private()
        ->args([service('jms_serializer.expression_evaluator')])
        ->tag('hateoas.relation_provider');

    $services->set('hateoas.configuration.metadata.yaml_driver', \Hateoas\Configuration\Metadata\Driver\YamlDriver::class)
        ->private()
        ->args([
            service('jms_serializer.metadata.file_locator'),
            service('jms_serializer.expression_evaluator'),
            service('hateoas.configuration.provider'),
            service('jms_serializer.type_parser')->nullOnInvalid(),
        ]);

    $services->set('hateoas.configuration.metadata.xml_driver', \Hateoas\Configuration\Metadata\Driver\XmlDriver::class)
        ->private()
        ->args([
            service('jms_serializer.metadata.file_locator'),
            service('jms_serializer.expression_evaluator'),
            service('hateoas.configuration.provider'),
            service('jms_serializer.type_parser')->nullOnInvalid(),
        ]);

    // The `Hateoas\Configuration\Metadata\Driver\AnnotationDriver` class and its corresponding `hateoas.configuration.metadata.annotation_driver` service are deprecated in favor of the `hateoas.configuration.metadata.attribute_driver` service
    $services->set('hateoas.configuration.metadata.annotation_driver', \Hateoas\Configuration\Metadata\Driver\AnnotationDriver::class)
        ->private()
        ->args([
            service('hateoas.configuration.metadata.annotation_reader'),
            service('jms_serializer.expression_evaluator'),
            service('hateoas.configuration.provider'),
            service('jms_serializer.type_parser')->nullOnInvalid(),
        ]);

    $services->set('hateoas.configuration.metadata.attribute_driver', \Hateoas\Configuration\Metadata\Driver\AttributeDriver::class)
        ->private()
        ->args([
            service('jms_serializer.expression_evaluator'),
            service('hateoas.configuration.provider'),
            service('jms_serializer.type_parser')->nullOnInvalid(),
        ]);

    // The `hateoas.configuration.metadata.annotation_or_attribute_driver` is necessary to provide the extension feature to the `hateoas.configuration.metadata.attribute_driver` service
    $services->set('hateoas.configuration.metadata.annotation_or_attribute_driver', \Metadata\Driver\DriverChain::class)
        ->private()
        ->args([
            [
                service('hateoas.configuration.metadata.attribute_driver')->ignoreOnInvalid(),
                service('hateoas.configuration.metadata.annotation_driver')->ignoreOnInvalid(),
            ],
        ]);

    $services->set('hateoas.configuration.metadata.extension_driver', \Hateoas\Configuration\Metadata\Driver\ExtensionDriver::class)
        ->private()
        ->args([service('hateoas.configuration.metadata.annotation_or_attribute_driver')]);

    $services->set('hateoas.configuration.metadata.chain_driver', \Metadata\Driver\DriverChain::class)
        ->private()
        ->args([
            [
                service('hateoas.configuration.metadata.yaml_driver'),
                service('hateoas.configuration.metadata.xml_driver'),
                service('hateoas.configuration.metadata.extension_driver'),
            ],
        ]);

    $services->alias('hateoas.configuration.metadata_driver', 'hateoas.configuration.metadata.chain_driver')
        ->public();

    $services->set('hateoas.configuration.metadata.lazy_loading_driver', \Metadata\Driver\LazyLoadingDriver::class)
        ->private()
        ->args([
            service('service_container'),
            'hateoas.configuration.metadata_driver',
        ]);

    // Metadata Factory
    $services->set('hateoas.configuration.metadata.cache.file_cache', \Metadata\Cache\FileCache::class)
        ->private()
        ->args([
            '', // Directory
        ]);

    $services->set('hateoas.configuration.metadata.annotation_reader', \Doctrine\Common\Annotations\AnnotationReader::class)
        ->private();

    $services->alias('hateoas.configuration.metadata.cache', 'hateoas.configuration.metadata.cache.file_cache')
        ->private();

    $services->set('hateoas.configuration.metadata_factory', \Metadata\MetadataFactory::class)
        ->private()
        ->args([
            service('hateoas.configuration.metadata.lazy_loading_driver'),
            \Metadata\ClassHierarchyMetadata::class,
            '%kernel.debug%',
        ])
        ->call('setCache', [service('hateoas.configuration.metadata.cache')->ignoreOnInvalid()]);
};
