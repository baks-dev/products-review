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

namespace BaksDev\Products\Review\Repository\AverageProductRating;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Entity\Review\Category\ProductReviewCategory;
use BaksDev\Products\Review\Entity\Review\Criteria\ProductReviewCriteria;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Entity\Review\Product\ProductReviewProduct;
use BaksDev\Products\Review\Entity\Review\Status\ProductReviewStatus;
use BaksDev\Products\Review\Entity\Setting\Category\ProductReviewSettingCategory;
use BaksDev\Products\Review\Entity\Setting\Criteria\ProductReviewSettingCriteria;
use BaksDev\Products\Review\Entity\Setting\Criteria\Text\ProductReviewSettingCriteriaText;
use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEvent;
use BaksDev\Products\Review\Entity\Setting\ProductReviewSetting;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\Type\Status\ReviewStatus\Collection\ReviewStatusActive;
use Generator;
use BaksDev\Products\Review\Entity\Review\ProductReview;

final readonly class AverageProductRatingRepository implements AverageProductRatingInterface
{
    private ProductUid $product;

    public function __construct(private DBALQueryBuilder $queryBuilder) {}

    public function product(ProductUid $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function find(): Generator
    {
        $dbal = $this->queryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(ProductReviewProduct::class)
            ->where('product_review_product.value = :product')
            ->setParameter('product', $this->product,ProductUid::TYPE);

        $dbal
            ->join(
                'product_review_product',
                ProductReviewEvent::class,
                'product_review_event',
                'product_review_event.id = product_review_product.event'
            );

        $dbal->join(
            'product_review_event',
            ProductReview::class,
            'product_review',
            'product_review.event = product_review_event.id'
        );

        $dbal
            ->join(
                'product_review_event',
                ProductReviewStatus::class,
                'product_review_status',
                'product_review_status.event = product_review_event.id
                AND product_review_status.value = :status'
            )
            ->setParameter('status', new ReviewStatus(ReviewStatusActive::PARAM), ReviewStatus::TYPE);

        $dbal
            ->addSelect('SUM(product_review_criteria.rating) AS sum')
            ->addSelect('COUNT(product_review_criteria.rating) AS count')
            ->addSelect('product_review_criteria.criteria AS criteria')
            ->join(
                'product_review_event',
                ProductReviewCriteria::class,
                'product_review_criteria',
                'product_review_criteria.event = product_review_event.id'
            );

        $dbal
            ->join(
                'product_review_criteria',
                ProductReviewSettingCriteria::class,
                'setting_criteria',
                'setting_criteria.const = product_review_criteria.criteria'
            );

        $dbal
            ->join(
                'setting_criteria',
                ProductReviewSetting::class,
                'setting',
                'setting.event = setting_criteria.event'
            );

        /**
         * Необходимо проверить соответствие категории данной настройке (на случай, если категория была удалена из
         * настройки
         */
        $dbal
            ->join(
                'setting',
                ProductReviewSettingCategory::class,
                'setting_category',
                'setting_category.event = setting.event'
            );

        $dbal
            ->where('product_review_category.value = setting_category.value')
            ->join(
                'product_review_event',
                ProductReviewCategory::class,
                'product_review_category',
                'product_review_category.event = product_review_event.id'
            );

        $dbal
            ->addSelect('setting_criteria_text.value AS name')
            ->join(
                'setting_criteria',
                ProductReviewSettingCriteriaText::class,
                'setting_criteria_text',
                'setting_criteria.id = setting_criteria_text.criteria'
            );

        $dbal->allGroupByExclude();

        return $dbal->fetchAllHydrate(AverageProductRatingResult::class);
    }
}