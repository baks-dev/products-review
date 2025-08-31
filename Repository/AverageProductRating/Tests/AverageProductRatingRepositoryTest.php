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

namespace BaksDev\Products\Review\Repository\AverageProductRating\Tests;

use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingInterface;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingRepository;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingResult;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Tests\EditProductReviewTest;

/**
 * @group products-review
 * @group products-review-repository
 *
 * @depends BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Tests\EditProductReviewTest::class
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-repository')]
final class AverageProductRatingRepositoryTest extends KernelTestCase
{
    #[DependsOnClass(EditProductReviewTest::class)]
    public function testFind(): void
    {
        $AverageProductRatingRepository = self::getContainer()->get(AverageProductRatingInterface::class);

        /** @var AverageProductRatingRepository $AverageProductRatingRepository */
        $result = $AverageProductRatingRepository
            ->product(new ProductUid())
            ->find()
            ->current();

        self::assertInstanceOf(AverageProductRatingResult::class, $result);
        self::assertInstanceOf(ProductReviewSettingCriteriaConst::class, $result->getCriteria());
        self::assertIsFloat($result->getAvg());
        self::assertIsInt($result->getSum());
        self::assertIsInt($result->getCount());
        self::assertIsString($result->getName());
    }
}