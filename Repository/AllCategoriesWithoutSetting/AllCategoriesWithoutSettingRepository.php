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

namespace BaksDev\Products\Review\Repository\AllCategoriesWithoutSetting;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Review\Entity\Setting\Category\ProductReviewSettingCategory;
use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEvent;
use BaksDev\Products\Review\Entity\Setting\ProductReviewSetting;
use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventUid;
use Doctrine\DBAL\Exception;
use Generator;

final class AllCategoriesWithoutSettingRepository implements AllCategoriesWithoutSettingInterface
{
    private ?ProductReviewSettingEventUid $event = null;

    public function __construct(private readonly DBALQueryBuilder $queryBuilder) {}

    public function event(ProductReviewSettingEventUid|ProductReviewSettingEvent|string $event): self
    {
        if(empty($event))
        {
            $this->event = null;
            return $this;
        }

        if(is_string($event))
        {
            $event = new ProductReviewSettingEventUid($event);
        }

        if($event instanceof ProductReviewSettingEvent)
        {
            $event = $event->getId();
        }

        $this->event = $event;
        return $this;
    }

    /**
     *
     * Метод получает список категорий, для которых нет настройки критериев. Он также позволяет получать помимо них
     * категории, которые уже являются частью данной настройки, если был указан uid события настройки критериев (метод
     * event)
     *
     * @return Generator<CategoryProductUid>|false
     * @throws Exception
     *
     */
    public function findAll(): Generator|false
    {
        $dbal = $this->queryBuilder->createQueryBuilder(self::class)->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            ->from(CategoryProduct::class, 'category');

        $dbal
            ->addSelect('category_event.sort')
            ->addSelect('category_event.parent')
            ->joinRecursive(
                'category',
                CategoryProductEvent::class,
                'category_event',
                'category_event.id = category.event',
            );

        $dbal
            ->addSelect('category_trans.name')
            ->leftJoin(
                'category',
                CategoryProductTrans::class,
                'category_trans',
                'category_trans.event = category.event AND category_trans.local = :local',
            );

        /** exist */
        $dbalExist = $this->queryBuilder->createQueryBuilder(self::class);

        $dbalExist
            ->select('category_exist.id')
            ->from(CategoryProduct::class, 'category_exist');

        $dbalExist
            ->leftJoin(
                'category_exist',
                ProductReviewSettingCategory::class,
                'review_setting_category_exist',
                'review_setting_category_exist.value = category_exist.id',
            );

        $dbalExist
            ->join(
                'review_setting_category_exist',
                ProductReviewSetting::class,
                'product_review_setting_exist',
                'product_review_setting_exist.event = review_setting_category_exist.event',
            );


        if($this->event instanceof ProductReviewSettingEventUid)
        {
            $dbalExist->where('product_review_setting_exist.event != :event');
        }

        $dbal
            ->where('category.id NOT IN ('.$dbalExist->getSQL().')')
            ->setParameter('event', $this->event, ProductReviewSettingEventUid::TYPE);

        $result = $dbal
            ->enableCache('products-review')
            ->findAllRecursive(['parent' => 'id']);

        if(false === $result)
        {
            return false;
        }

        foreach($result as $item)
        {
            yield new CategoryProductUid($item['id'], $item['name'], $item['level']);
        }
    }
}