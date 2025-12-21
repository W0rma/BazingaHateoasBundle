<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    // Factories
    $services->set('hateoas.link_factory', \Hateoas\Factory\LinkFactory::class)
        ->private()
        ->args([
            service('hateoas.generator.registry'),
            service('jms_serializer.expression_evaluator'),
        ]);

    $services->set('hateoas.links_factory', \Hateoas\Factory\LinksFactory::class)
        ->private()
        ->args([
            service('hateoas.configuration.metadata_factory'),
            service('hateoas.link_factory'),
            service('hateoas.serializer.exclusion_manager'),
        ]);

    $services->set('hateoas.embeds_factory', \Hateoas\Factory\EmbeddedsFactory::class)
        ->private()
        ->args([
            service('hateoas.configuration.metadata_factory'),
            service('jms_serializer.expression_evaluator'),
            service('hateoas.serializer.exclusion_manager'),
        ]);

    // Serializers & Handlers
    $services->set('hateoas.serializer.xml', \Hateoas\Serializer\XmlSerializer::class)
        ->private();

    $services->set('hateoas.serializer.json_hal', \Hateoas\Serializer\JsonHalSerializer::class)
        ->private();

    $services->set('hateoas.serializer.exclusion.expression_language_strategy', \JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy::class)
        ->private()
        ->args([service('jms_serializer.expression_evaluator')]);

    $services->set('hateoas.serializer.exclusion_manager', \Hateoas\Serializer\ExclusionManager::class)
        ->args([service('hateoas.serializer.exclusion.expression_language_strategy')]);

    // Subscribers
    $services->set('hateoas.event_listener.xml', \Hateoas\Serializer\AddRelationsListener::class)
        ->args([
            '', // xml serializer
            service('hateoas.links_factory'),
            service('hateoas.embeds_factory'),
            service('hateoas.inline_deferrer.embeds'),
            service('hateoas.inline_deferrer.links'),
        ])
        ->tag('jms_serializer.event_listener', ['event' => 'serializer.post_serialize', 'format' => 'xml', 'method' => 'onPostSerialize']);

    $services->set('hateoas.event_listener.json', \Hateoas\Serializer\AddRelationsListener::class)
        ->args([
            '', // json serializer
            service('hateoas.links_factory'),
            service('hateoas.embeds_factory'),
            service('hateoas.inline_deferrer.embeds'),
            service('hateoas.inline_deferrer.links'),
        ])
        ->tag('jms_serializer.event_listener', ['event' => 'serializer.post_serialize', 'format' => 'json', 'method' => 'onPostSerialize']);

    $services->set('hateoas.inline_deferrer.embeds', \Hateoas\Serializer\Metadata\InlineDeferrer::class)
        ->private();

    $services->set('hateoas.inline_deferrer.links', \Hateoas\Serializer\Metadata\InlineDeferrer::class)
        ->private();
};
