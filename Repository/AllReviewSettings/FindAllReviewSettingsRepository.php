<?php
/*
 * Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Products\Review\Repository\AllReviewSettings;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\Paginator;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Review\Entity\Setting\Category\ProductReviewSettingCategory;
use BaksDev\Products\Review\Entity\Setting\Criteria\ProductReviewSettingCriteria;
use BaksDev\Products\Review\Entity\Setting\Criteria\Text\ProductReviewSettingCriteriaText;
use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEvent;
use BaksDev\Products\Review\Entity\Setting\ProductReviewSetting;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;

final class FindAllReviewSettingsRepository implements FindAllReviewSettingsInterface
{
    private ?SearchDTO $search = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly Paginator $paginator,
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    /** @return Paginator */
    public function findAll(): Paginator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();
        $dbal
            ->select('review_setting.id AS review_setting_id')
            ->addSelect('review_setting.event AS review_setting_event')
            ->from(ProductReviewSetting::class, 'review_setting');

        $dbal
            ->join(
                'review_setting',
                ProductReviewSettingEvent::class,
                'review_setting_event',
                'review_setting.event = review_setting_event.id'
            );

        $dbal
            ->join(
                'review_setting_event',
                ProductReviewSettingCategory::class,
                'review_setting_category',
                'review_setting_event.id = review_setting_category.event'
            );


        /** Названия категорий */
        $dbal
            ->leftJoin(
                'review_setting_category',
                CategoryProduct::class,
                'category_product',
                'category_product.id = review_setting_category.value'
            );

        $dbal
            ->leftJoin(
                'category_product',
                CategoryProductEvent::class,
                'category_product_event',
                'category_product_event.id = category_product.event'
            );

        $dbal
            ->addSelect("JSON_AGG (DISTINCT category_trans.name) AS categories")
            ->leftJoin(
                'category_product_event',
                CategoryProductTrans::class,
                'category_trans',
                'category_trans.event = category_product_event.id AND category_trans.local = :local'
            );


        /** Названия критериев */
        $dbal
            ->leftJoin(
                'review_setting_event',
                ProductReviewSettingCriteria::class,
                'review_setting_criteria',
                'review_setting_criteria.event = review_setting_event.id'
            );

        $dbal
            ->addSelect("JSON_AGG (
                    DISTINCT review_setting_criteria_text.value
                ) AS criteria")
            ->leftJoin(
                'review_setting_criteria',
                ProductReviewSettingCriteriaText::class,
                'review_setting_criteria_text',
                'review_setting_criteria_text.criteria = review_setting_criteria.id'
            );

        /* Поиск */
        if(false === empty($this->search) && $this->search->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('category_trans.name');
        }

        $dbal->allGroupByExclude();
        return $this->paginator->fetchAllHydrate($dbal, FindAllReviewSettingsResult::class);
    }
}