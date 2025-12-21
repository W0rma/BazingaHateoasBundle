<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('hateoas.generator.registry', \Hateoas\UrlGenerator\UrlGeneratorRegistry::class)
        ->public()
        ->args([service('hateoas.generator.symfony')]);

    $services->set('hateoas.generator.symfony', \Hateoas\UrlGenerator\SymfonyUrlGenerator::class)
        ->args([service('router')]);
};
