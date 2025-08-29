<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Review\BaksDevProductsReviewBundle;

return static function(ContainerConfigurator $configurator): void {

    $services = $configurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Пример конфигурации относительно класса бандла
    $NAMESPACE = BaksDevProductsReviewBundle::NAMESPACE;
    $PATH = BaksDevProductsReviewBundle::PATH;

    $services->load($NAMESPACE, $PATH)
        ->exclude([
            $PATH.'{Entity,Resources,Type}',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*Message.php',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*Result.php',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*DTO.php',
            $PATH.'**'.DIRECTORY_SEPARATOR.'*Test.php',
        ]);

    /* Статусы заказов */
    $services->load(
        $NAMESPACE.'Type\Status\ReviewStatus\\',
        $PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'Status', 'ReviewStatus'])
    );

    $services->load($NAMESPACE.'Form\Rating\\', $PATH.implode(DIRECTORY_SEPARATOR, ['Form', 'Rating'])); //  'Form/Rating');
};