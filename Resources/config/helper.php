<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('hateoas.helper.link', \Hateoas\Helper\LinkHelper::class)
        ->public()
        ->args([
            service('hateoas.link_factory'),
            service('hateoas.configuration.metadata_factory'),
        ]);

    $services->set('hateoas.expression.link_expression_function', \Bazinga\Bundle\HateoasBundle\Expression\LinkExpressionFunction::class)
        ->private()
        ->tag('jms.expression.function_provider');
};
