<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Review\Messenger\Status;

use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\Type\Status\ReviewStatus\Collection\ReviewStatusActive;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\EditProductReviewDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\EditProductReviewHandler;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Status\EditProductReviewStatusDTO;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Изменение статуса отзыва
 */
#[AsMessageHandler(priority: 0)]
#[Autoconfigure(shared: false)]
final readonly class ChangeStatusProductReviewDispatcher
{
    public function __construct(
        private EditProductReviewHandler $EditProductReviewHandler,
        #[Target('productsReviewLogger')] private LoggerInterface $Logger,
        private EntityManagerInterface $EntityManager,
    ) {}

    public function __invoke(ChangeStatusProductReviewMessage $message): void
    {
        $ProductReviewEvent = $this->EntityManager
            ->getRepository(ProductReviewEvent::class)
            ->find($message->getEvent());

        $EditProductReviewDTO = new EditProductReviewDTO();

        $ProductReviewEvent->getDto($EditProductReviewDTO);

        $EditProductReviewStatusDTO = new EditProductReviewStatusDTO()
            ->setValue(new ReviewStatus(ReviewStatusActive::class));

        $EditProductReviewDTO->setStatus($EditProductReviewStatusDTO);

        $handle = $this->EditProductReviewHandler->handle($EditProductReviewDTO);

        if(false === ($handle instanceof EditProductReviewDTO))
        {
            $this->Logger->critical(sprintf(
                'Ошибка при попытке изменить статус отзыва с текущим событием %s',
                $message->getEvent()
            ));
        }
    }
}