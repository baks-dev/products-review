<?php


namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Review\BaksDevProductsReviewBundle;
use Symfony\Config\TwigConfig;

return static function(TwigConfig $twig) {

    $PATH = BaksDevProductsReviewBundle::PATH;

    $twig->path(
        $PATH.implode(DIRECTORY_SEPARATOR, ['Resources', 'view', '']),
        'products-review',
    );
};