<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Review\BaksDevProductsReviewBundle;
use BaksDev\Products\Review\Type\Average\Criteria\ProductReviewAverageCriteriaType;
use BaksDev\Products\Review\Type\Average\Criteria\ProductReviewAverageCriteriaUid;
use BaksDev\Products\Review\Type\Review\Criteria\Id\ProductReviewCriteriaType;
use BaksDev\Products\Review\Type\Review\Criteria\Id\ProductReviewCriteriaUid;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventType;
use BaksDev\Products\Review\Type\Review\Id\ProductReviewType;
use BaksDev\Products\Review\Type\Review\Id\ProductReviewUid;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConstType;
use BaksDev\Products\Review\Type\Setting\Criteria\Id\ProductReviewSettingCriteriaType;
use BaksDev\Products\Review\Type\Setting\Criteria\Id\ProductReviewSettingCriteriaUid;
use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventType;
use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventUid;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingType;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingUid;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\Type\Status\ReviewStatusType;
use Symfony\Config\DoctrineConfig;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;

return static function (DoctrineConfig $doctrine): void {

    $doctrine->dbal()->type(ProductReviewUid::TYPE)->class(ProductReviewType::class);
    $doctrine->dbal()->type(ProductReviewEventUid::TYPE)->class(ProductReviewEventType::class);
    $doctrine->dbal()->type(ProductReviewSettingCriteriaUid::TYPE)->class(ProductReviewSettingCriteriaType::class);
    $doctrine->dbal()->type(ProductReviewSettingEventUid::TYPE)->class(ProductReviewSettingEventType::class);
    $doctrine->dbal()->type(ProductReviewSettingUid::TYPE)->class(ProductReviewSettingType::class);
    $doctrine->dbal()->type(ReviewStatus::TYPE)->class(ReviewStatusType::class);
    $doctrine->dbal()->type(ProductReviewAverageCriteriaUid::TYPE)->class(ProductReviewAverageCriteriaType::class);
    $doctrine
        ->dbal()
        ->type(ProductReviewSettingCriteriaConst::TYPE)
        ->class(ProductReviewSettingCriteriaConstType::class);
    $doctrine->dbal()->type(ProductReviewCriteriaUid::TYPE)->class(ProductReviewCriteriaType::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $NAMESPACE = BaksDevProductsReviewBundle::NAMESPACE;
    $PATH = BaksDevProductsReviewBundle::PATH;

    $emDefault
        ->mapping('product-review')
        ->type('attribute')
        ->dir($PATH.'Entity')
        ->isBundle(false)
        ->prefix($NAMESPACE.'Entity')
        ->alias('product-review');
};
