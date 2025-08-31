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

namespace BaksDev\Products\Review\Repository\AllReviews\Tests;

use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Product\UseCase\Admin\NewEdit\Tests\ProductsProductNewAdminUseCaseTest;
use BaksDev\Products\Review\Repository\AllReviews\AllReviewsInterface;
use BaksDev\Products\Review\Repository\AllReviews\AllReviewsRepository;
use BaksDev\Products\Review\Repository\AllReviews\AllReviewsResult;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Tests\NewProductReviewTest;
use BaksDev\Users\Profile\UserProfile\UseCase\User\NewEdit\Tests\UserNewUserProfileHandleTest;
use BaksDev\Users\User\Type\Id\UserUid;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('products-review')]
final class AllReviewsRepositoryTest extends KernelTestCase
{
    #[DependsOnClass(NewProductReviewTest::class)]
    #[DependsOnClass(UserNewUserProfileHandleTest::class)]
    #[DependsOnClass(ProductsProductNewAdminUseCaseTest::class)]
    public function testPaginator()
    {
        $AllReviewsRepository = self::getContainer()->get(AllReviewsInterface::class);

        /** @var AllReviewsRepository $AllReviewsRepository */
        $results = $AllReviewsRepository
            ->product(new ProductUid(ProductUid::TEST))
            ->findPaginator()
            ->getData();

        foreach ($results as $result)
        {
            /** @var AllReviewsResult $result */
            self::assertInstanceOf(AllReviewsResult::class, $result);

            self::assertTrue(
                $result->getReviewEvent() === false
                || $result->getReviewEvent() instanceof ProductReviewEventUid
            );

            self::assertInstanceOf(ProductUid::class, $result->getReviewProduct());

            self::assertIsString($result->getProductName());

            self::assertInstanceOf(ReviewStatus::class, $result->getReviewStatus());

            self::assertIsString($result->getReviewText());

            self::assertInstanceOf(UserUid::class, $result->getReviewUser());

            self::assertIsString($result->getProfileUsername());

            self::assertTrue($result->getReviewName() === null || is_string($result->getReviewName()));

            self::assertInstanceOf(DateTimeImmutable::class, $result->getUpdate());

            self::assertIsFloat($result->getReviewRatingValue());

            return;
        }
    }
}