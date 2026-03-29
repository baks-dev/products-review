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

namespace BaksDev\Products\Review\Repository\AllReviews;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Products\Product\Entity\Event\ProductEvent;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Product\Entity\Trans\ProductTrans;
use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Entity\Review\Modify\ProductReviewModify;
use BaksDev\Products\Review\Entity\Review\Name\ProductReviewName;
use BaksDev\Products\Review\Entity\Review\Product\ProductReviewProduct;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\Entity\Review\Profile\ProductReviewProfile;
use BaksDev\Products\Review\Entity\Review\Rating\ProductReviewRating;
use BaksDev\Products\Review\Entity\Review\Status\ProductReviewStatus;
use BaksDev\Products\Review\Entity\Review\Text\ProductReviewText;
use BaksDev\Products\Review\Entity\Review\Type\ProductReviewType;
use BaksDev\Products\Review\Entity\Review\User\ProductReviewUser;
use BaksDev\Products\Review\Form\ReviewFilter\Admin\ProductReviewFilterDTO;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\Type\Status\ReviewStatus\Collection\ReviewStatusActive;
use BaksDev\Users\Profile\TypeProfile\Entity\Trans\TypeProfileTrans;
use BaksDev\Users\Profile\TypeProfile\Entity\TypeProfile;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Personal\UserProfilePersonal;
use BaksDev\Users\Profile\UserProfile\Entity\Event\UserProfileEvent;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Generator;

final class AllReviewsRepository implements AllReviewsInterface
{

    private ?ProductReviewFilterDTO $filter = null;

    private ProductUid $product;

    private ?SearchDTO $search = null;


    /**
     * Флаг для отображения всех отзывов (всех складов)
     */

    private bool $allProjects = false;


    /**
     * Флаг для задания активности всех отзывов (всех складов)
     * по умолчанию выводим активные
     */

    private bool $active = true;


    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}


    public function filter(ProductReviewFilterDTO $filter): self
    {
        $this->filter = $filter;
        return $this;
    }

    public function product(ProductUid $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function setAllProjects(bool $allProjects): self
    {
        $this->allProjects = $allProjects;
        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }


    public function search(SearchDTO $search): static
    {
        $this->search = $search;
        return $this;
    }

    public function findPaginator(): PaginatorInterface
    {
        $dbal = $this->builder();

        return $this->paginator->fetchAllHydrate($dbal, AllReviewsResult::class);
    }

    private function builder(): DBALQueryBuilder
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class)->bindLocal();


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
            $dbal->setParameter('product', $this->product, ProductUid::TYPE);
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


        /** Status */

        $dbal
            ->addSelect('review_status.value AS review_status')
            ->join(
                'review_event',
                ProductReviewStatus::class,
                'review_status',
                'review_status.event = review_event.id'.(true === $this->active ? ' AND review_status.value = :status' : ''),
            );

        if(true === $this->active)
        {
            $dbal->setParameter(
                'status',
                ReviewStatusActive::PARAM,
                ReviewStatus::TYPE,
            );
        }


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


        /** Type */

        $dbal
            ->leftJoin(
                'review_event',
                ProductReviewType::class,
                'review_type',
                'review_type.event = review_event.id',
            );

        /* TypeProfile */
        $dbal
            ->leftJoin(
                'review_type',
                TypeProfile::class,
                'type_users_profile',
                'type_users_profile.id = review_type.type',
            );

        /* TypeProfileTrans для вывода Типа профиля */
        $dbal
            ->addSelect('type_users_profile_trans.name as type_profile_name')
            ->leftJoin(
                'type_users_profile',
                TypeProfileTrans::class,
                'type_users_profile_trans',
                'type_users_profile_trans.event = type_users_profile.event',
            );


        /** Profile */

        $dbal
            ->leftJoin(
                'review_event',
                ProductReviewProfile::class,
                'review_profile',
                'review_profile.event = review_event.id',
            );


        /** UserProfile */

        $dbal
            ->leftJoin(
                'review_profile',
                UserProfile::class,
                'project_profile',
                'project_profile.id = review_profile.value',
            );

        // Event
        $dbal->leftJoin(
            'project_profile',
            UserProfileEvent::class,
            'users_profile_event',
            'users_profile_event.id = project_profile.event',
        );

        // Personal
        $dbal
            ->addSelect('users_profile_personal.username AS users_profile_username')
            ->leftJoin(
                'users_profile_event',
                UserProfilePersonal::class,
                'users_profile_personal',
                'users_profile_personal.event = users_profile_event.id',
            );


        /* Фильтр по status */
        if(false === empty($this->filter) && false === empty($this->filter->getStatus()))
        {
            $dbal
                ->where('review_status.value = :status')
                ->setParameter('status', $this->filter->getStatus(), ReviewStatus::TYPE);
        }


        /* Фильтр по profile */
        if(false === empty($this->filter) && false === empty($this->filter->getProfile()))
        {
            $dbal
                ->andWhere('review_profile.value = :profile')
                ->setParameter(
                    'profile',
                    $this->filter->getProfile(),
                    UserProfileUid::TYPE,
                );
        }


        /* Если выводить не для всех складов */
        if(false === $this->allProjects && true === $dbal->bindProjectProfile())
        {
            // Отзывы с учетом PROJECT_PROFILE либо NULL
            $dbal->andWhere('project_profile.id = :'.$dbal::PROJECT_PROFILE_KEY.' OR review_profile.value IS NULL');
        }


        /* Поиск */
        if(true === ($this->search instanceof SearchDTO) && $this->search->getQuery())
        {

            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('review_name.value')
                ->addSearchLike('review_text.value')
                ->addSearchLike('product_trans.name');
        }


        $dbal->orderBy('review.id', 'DESC');

        $dbal->allGroupByExclude();

        $dbal->addGroupBy('review.id');

        return $dbal;
    }

    public function findAll(): Generator
    {
        $dbal = $this->builder();

        return $dbal->fetchAllHydrate(AllReviewsResult::class);
    }
}