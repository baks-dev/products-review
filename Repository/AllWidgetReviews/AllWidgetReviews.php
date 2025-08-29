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

namespace BaksDev\Products\Review\Repository\AllWidgetReviews;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Product\Entity\Category\ProductCategory;
use BaksDev\Products\Product\Entity\Event\ProductEvent;
use BaksDev\Products\Product\Entity\Info\ProductInfo;
use BaksDev\Products\Product\Entity\Photo\ProductPhoto;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Product\Entity\Trans\ProductTrans;
use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Entity\Average\Counter\ProductReviewAverageCounter;
use BaksDev\Products\Review\Entity\Average\ProductReviewAverage;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Entity\Review\Modify\ProductReviewModify;
use BaksDev\Products\Review\Entity\Review\Name\ProductReviewName;
use BaksDev\Products\Review\Entity\Review\Product\ProductReviewProduct;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\Entity\Review\Rating\ProductReviewRating;
use BaksDev\Products\Review\Entity\Review\Status\ProductReviewStatus;
use BaksDev\Products\Review\Entity\Review\Text\ProductReviewText;
use BaksDev\Products\Review\Entity\Review\User\ProductReviewUser;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Personal\UserProfilePersonal;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use Generator;
use BaksDev\Products\Review\Type\Status\ReviewStatus\Collection\ReviewStatusActive;

final class AllWidgetReviews implements AllWidgetReviewsInterface
{
    private DBALQueryBuilder $DBALQueryBuilder;

    public function __construct(DBALQueryBuilder $DBALQueryBuilder)
    {
        $this->DBALQueryBuilder = $DBALQueryBuilder;
    }

    /** Метод возвращает пагинатор Reviews */
    public function findAll(): Generator|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('review.event AS review_event')
            ->from(ProductReview::class, 'review');

        $dbal
            ->join(
                'review',
                ProductReviewEvent::class,
                'review_event',
                'review.event = review_event.id',
            );


        /* Модификатор */
        $dbal->addSelect('review_modify.mod_date as update');
        $dbal->join(
            'review_event',
            ProductReviewModify::class,
            'review_modify',
            'review_modify.event = review_event.id',
        );


        /** Product */
        $dbal
            ->addSelect('review_product.value AS review_product')
            ->join(
                'review_event',
                ProductReviewProduct::class,
                'review_product',
                'review_product.event = review_event.id',
            );

        $dbal
            ->join(
                'review_product',
                Product::class,
                'product',
                'product.id = review_product.value'.(empty($this->product) ? '' : ' AND product.id = :product'),
            );

        if(false === empty($this->product))
        {
            $dbal->setParameter('product', '$this->product', ProductUid::TYPE);
        }

        $dbal
            ->join(
                'product',
                ProductEvent::class,
                'product_event',
                'product_event.id = product.event',
            );

        $dbal
            ->addSelect('product_trans.name AS product_name')
            ->join(
                'product_event',
                ProductTrans::class,
                'product_trans',
                'product_trans.event = product_event.id',
            );

        $dbal
            ->leftJoin(
                'product_event',
                ProductPhoto::class,
                'product_photo',
                'product_photo.event = product_event.id AND product_photo.root = TRUE',
            );

        $dbal->addSelect("
            JSON_AGG 
                (DISTINCT
                    CASE
                        WHEN product_photo.ext IS NOT NULL THEN
                            JSONB_BUILD_OBJECT
                            (
                                'product_img_root', product_photo.root,
                                'product_img', CONCAT ( '/upload/".$dbal->table(ProductPhoto::class)."' , '/', product_photo.name),
                                'product_img_ext', product_photo.ext,
                                'product_img_cdn', product_photo.cdn
                            )
                        ELSE NULL
                END)
            AS product_images",
        );

        $dbal
            ->leftJoin(
                'product_event',
                ProductCategory::class,
                'product_category',
                'product_category.event = product_event.id'
            );

        $dbal
            ->leftJoin(
                'product_event',
                CategoryProduct::class,
                'category',
                'category.id = product_category.category'
            );

        $dbal
            ->addSelect('category_info.url AS category_url')
            ->leftJoin(
                'category',
                CategoryProductInfo::class,
                'category_info',
                'category_info.event = category.event'
            );

        $dbal
            ->addSelect('product_info.url as product_url')
            ->leftJoin(
                'product_event',
                ProductInfo::class,
                'product_info',
                'product_info.product = product.id'
            );


        /** Status */
        $dbal
            ->addSelect('review_status.value AS review_status')
            ->join(
                'review_event',
                ProductReviewStatus::class,
                'review_status',
                'review_status.event = review_event.id AND review_status.value = :status',
            )
            ->setParameter('status', ReviewStatusActive::PARAM, ReviewStatus::TYPE);


        /** Text */
        $dbal
            ->addSelect('review_text.value AS review_text')
            ->join(
                'review_event',
                ProductReviewText::class,
                'review_text',
                'review_text.event = review_event.id',
            );


        /** User */
        $dbal
            ->addSelect('review_user.value AS review_user')
            ->join(
                'review_event',
                ProductReviewUser::class,
                'review_user',
                'review_user.event = review_event.id',
            );

        $dbal
            ->join(
                'review_user',
                UserProfileInfo::class,
                'profile_info',
                'profile_info.usr = review_user.value AND profile_info.active = true');

        $dbal
            ->addSelect('review_rating.value AS review_rating_value')
            ->leftJoin(
                'review_event',
                ProductReviewRating::class,
                'review_rating',
                'review_rating.event = review_event.id',
            );

        $dbal->leftJoin(
            'profile_info',
            UserProfile::class,
            'profile',
            'profile.id = profile_info.profile',
        );

        $dbal
            ->addSelect('profile_personal.username as profile_username')
            ->leftJoin(
                'profile',
                UserProfilePersonal::class,
                'profile_personal',
                'profile_personal.event = profile.event',
            );


        /** Name */
        $dbal
            ->addSelect('review_name.value AS review_name')
            ->leftJoin(
                'review_event',
                ProductReviewName::class,
                'review_name',
                'review_name.event = review_event.id',
            );


        /** Reviews count */
        $dbal
            ->join(
                'review_product',
                ProductReviewAverage::class,
                'review_average',
                'review_average.product = review_product.value'
            );

        $dbal
            ->addSelect('review_average_counter.value AS count')
            ->join(
                'review_average',
                ProductReviewAverageCounter::class,
                'review_average_counter',
                'review_average_counter.product = review_average.product'
            );

        $dbal->orderBy('review.id', 'DESC');

        $dbal->allGroupByExclude();

        $dbal->addGroupBy('review.id');

        return $dbal->setMaxResults(3)->fetchAllHydrate(AllWidgetReviewsResult::class);
    }
}
