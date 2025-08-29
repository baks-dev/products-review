<?php

use BaksDev\Products\Review\BaksDevProductsReviewBundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;


return function(RoutingConfigurator $routes) {

    $PATH = BaksDevProductsReviewBundle::PATH;

    $routes->import(
        $PATH.'Controller',
        'attribute',
        false,
        $PATH.implode(DIRECTORY_SEPARATOR, ['Controller', '**', '*Test.php']),
    )
        ->prefix(\BaksDev\Core\Type\Locale\Locale::routes())
        ->namePrefix('products-review:');
};