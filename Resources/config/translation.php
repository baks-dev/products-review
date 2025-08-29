<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Review\BaksDevProductsReviewBundle;
use BaksDev\Support\BaksDevSupportBundle;
use Symfony\Config\FrameworkConfig;

return static function(FrameworkConfig $config) {

    $PATH = BaksDevProductsReviewBundle::PATH;

    $config
        ->translator()
        ->paths([$PATH.implode(DIRECTORY_SEPARATOR, ['Resources', 'translations', ''])]);
};