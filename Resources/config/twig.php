<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('hateoas.twig.link', \Hateoas\Twig\Extension\LinkExtension::class)
        ->args([service('hateoas.helper.link')])
        ->tag('twig.extension');
};
