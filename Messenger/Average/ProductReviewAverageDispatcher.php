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

namespace BaksDev\Products\Review\Messenger\Average;

use BaksDev\Products\Review\Entity\Average\ProductReviewAverage;
use BaksDev\Products\Review\Messenger\ProductReviewMessage;
use BaksDev\Products\Review\Repository\AllReviews\AllReviewsInterface;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingInterface;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingResult;
use BaksDev\Products\Review\Repository\ReviewCurrentEvent\ReviewCurrentEventInterface;
use BaksDev\Products\Review\UseCase\Average\NewEdit\Counter\NewEditProductReviewAverageAverageCounterDTO;
use BaksDev\Products\Review\UseCase\Average\NewEdit\Criteria\NewEditProductReviewAverageCriteriaDTO;
use BaksDev\Products\Review\UseCase\Average\NewEdit\NewEditProductReviewAverageDTO;
use BaksDev\Products\Review\UseCase\Average\NewEdit\NewEditProductReviewAverageHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Обновляет средние оценки товаров
 */
#[AsMessageHandler(priority: 0)]
final readonly class ProductReviewAverageDispatcher
{
    public function __construct(
        private AverageProductRatingInterface $AverageProductRatingRepository,
        private NewEditProductReviewAverageHandler $NewEditAverageHandler,
        #[Target('productsReviewLogger')] private LoggerInterface $logger,
        private ReviewCurrentEventInterface $ReviewCurrentEventRepository,
        private AllReviewsInterface $AllReviews,
    ) {}

    public function __invoke(ProductReviewMessage $message): void
    {
        $currentEvent = $this->ReviewCurrentEventRepository->get((string) $message->getId());

        if(true === empty($currentEvent))
        {
            return;
        }

        $product = $currentEvent->getProduct()->getValue();

        $result = iterator_to_array($this->AverageProductRatingRepository->product($product)->find());


        /** Продукт */
        $averageDTO = new NewEditProductReviewAverageDTO()->setProduct($product);


        /** Кол-во отзывов */
        $allReviews = $this->AllReviews->product($product)->findAll();
        $counter = count(iterator_to_array($allReviews));
        $counterDTO = new NewEditProductReviewAverageAverageCounterDTO()->setValue($counter);
        $averageDTO->setCounter($counterDTO);


        /** Рейтинг (общий и по критериям) */
        $averageSum = 0;
        $averageCount = 0;

        foreach($result as $review)
        {
            if(true === empty($review->getAvg))
            {
                continue;
            }

            $averageCriteriaDTO = new NewEditProductReviewAverageCriteriaDTO();

            /** @var AverageProductRatingResult $review */
            $averageCriteriaDTO->setCriteria($review->getCriteria());
            $averageCriteriaDTO->setValue($review->getAvg());

            $averageDTO->addCriterion($averageCriteriaDTO);

            $averageSum += $review->getAvg();
            $averageCount += 1;
        }

        $averageDTO->setValue(5);

        if($averageCount > 0)
        {
            $averageDTO->setValue(round($averageSum / $averageCount, 1));
        }

        $handle = $this->NewEditAverageHandler->handle($averageDTO);

        if(false === $handle instanceof ProductReviewAverage)
        {
            $this->logger->critical(
                'products-review: Не удалось рассчитать средние оценки по продукту',
                [self::class.':'.__LINE__, var_export($message, true)]
            );
        }

        if(true === $handle instanceof ProductReviewAverage)
        {
            $this->logger->info(
                'products-review: Успешно обновлены средние оценки по продукту',
                [self::class.':'.__LINE__, var_export($message, true)]
            );
        }
    }
}