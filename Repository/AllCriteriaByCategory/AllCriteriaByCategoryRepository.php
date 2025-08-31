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

namespace BaksDev\Products\Review\Repository\AllCriteriaByCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Review\Entity\Setting\Category\ProductReviewSettingCategory;
use BaksDev\Products\Review\Entity\Setting\Criteria\ProductReviewSettingCriteria;
use BaksDev\Products\Review\Entity\Setting\Criteria\Text\ProductReviewSettingCriteriaText;
use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEvent;
use BaksDev\Products\Review\Entity\Setting\ProductReviewSetting;
use Generator;
use InvalidArgumentException;

final readonly class AllCriteriaByCategoryRepository implements AllCriteriaByCategoryInterface
{
    private ?CategoryProductUid $category;

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    public function category(CategoryProductUid|string $category): self
    {
        if(true === empty($category))
        {
            return $this;
        }

        if(true === is_string($category))
        {
            $category = new CategoryProductUid($category);
        }

        $this->category = $category;

        return $this;
    }

    public function findAll(): Generator
    {
        if(true === empty($this->category))
        {
            throw new InvalidArgumentException('Не передан обязательный параметр запроса category');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('setting_criteria.const AS criteria')
            ->from(ProductReviewSettingCriteria::class, 'setting_criteria');

        $dbal
            ->addSelect('setting_criteria_text.value AS name')
            ->join(
                'setting_criteria',
                ProductReviewSettingCriteriaText::class,
                'setting_criteria_text',
                'setting_criteria.id = setting_criteria_text.criteria'
            );

        $dbal
            ->join(
                'setting_criteria',
                ProductReviewSettingEvent::class,
                'setting_event',
                'setting_criteria.event = setting_event.id'
            );

        $dbal
            ->join(
                'setting_event',
                ProductReviewSetting::class,
                'setting',
                'setting.event = setting_event.id'
            );

        $dbal
            ->join(
                'setting_event',
                ProductReviewSettingCategory::class,
                'setting_category',
                'setting_category.event = setting_event.id'
            )
            ->where('setting_category.value = :category')
            ->setParameter('category', $this->category, CategoryProductUid::TYPE);

        return $dbal
            ->enableCache('products-review', 3600)
            ->fetchAllHydrate(AllCriteriaByCategoryResult::class);
    }
}