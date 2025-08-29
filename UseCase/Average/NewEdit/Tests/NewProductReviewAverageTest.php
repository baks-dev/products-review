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

namespace BaksDev\Products\Review\UseCase\Average\NewEdit\Tests;

use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Entity\Average\ProductReviewAverage;
use BaksDev\Products\Review\Repository\AllReviews\AllReviewsInterface;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingInterface;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingRepository;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingResult;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Tests\EditProductReviewTest;
use BaksDev\Products\Review\UseCase\Average\NewEdit\Counter\NewEditProductReviewAverageAverageCounterDTO;
use BaksDev\Products\Review\UseCase\Average\NewEdit\Criteria\NewEditProductReviewAverageCriteriaDTO;
use BaksDev\Products\Review\UseCase\Average\NewEdit\NewEditProductReviewAverageHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use BaksDev\Products\Review\UseCase\Average\NewEdit\NewEditProductReviewAverageDTO;
use Symfony\Component\DependencyInjection\Attribute\When;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @group products-review
 * @group products-review-usecase
 *
 * @depends BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Tests\EditProductReviewTest::class
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-usecase')]
final class NewProductReviewAverageTest extends KernelTestCase
{
    #[DependsOnClass(EditProductReviewTest::class)]
    public function testNew(): void
    {
        $AverageProductRatingRepository = self::getContainer()->get(AverageProductRatingInterface::class);

        /** @var AverageProductRatingRepository $AverageProductRatingRepository */
        $product = new ProductUid();

        $result = $AverageProductRatingRepository->product($product)->find();
        $averageDTO = new NewEditProductReviewAverageDTO();


        /** Кол-во отзывов */
        $AllReviewsRepository = self::getContainer()->get(AllReviewsInterface::class);
        $allReviews = $AllReviewsRepository->product($product)->findAll();
        $counter = count(iterator_to_array($allReviews));
        $counterDTO = new NewEditProductReviewAverageAverageCounterDTO()->setValue($counter);
        $averageDTO->setCounter($counterDTO);


        /** Продукт */
        $averageDTO->setProduct($product);


        /** Рейтинг (общий и по критериям) */
        $averageCriteriaDTO = new NewEditProductReviewAverageCriteriaDTO();

        $averageSum = 0;
        $averageCount = 0;

        foreach($result as $review)
        {
            /** @var AverageProductRatingResult $review */
            $averageCriteriaDTO->setCriteria($review->getCriteria());
            $averageCriteriaDTO->setValue($review->getAvg());

            $averageSum += $review->getAvg();
            $averageCount += 1;
        }

        $averageDTO
            ->setValue(round($averageSum / $averageCount, 1))
            ->addCriterion($averageCriteriaDTO);


        $NewEditAverageHandler = self::getContainer()->get(NewEditProductReviewAverageHandler::class);

        $handle = $NewEditAverageHandler->handle($averageDTO);

        self::assertInstanceOf(ProductReviewAverage::class, $handle);
    }
}