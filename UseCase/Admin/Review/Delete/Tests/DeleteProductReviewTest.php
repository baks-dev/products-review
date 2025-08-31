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

namespace BaksDev\Products\Review\UseCase\Admin\Review\Delete\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\Repository\ReviewCurrentEvent\ReviewCurrentEventInterface;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Products\Review\Type\Review\Id\ProductReviewUid;
use BaksDev\Products\Review\UseCase\Admin\Review\Delete\DeleteProductReviewDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\Delete\DeleteProductReviewHandler;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Tests\EditProductReviewTest;
use BaksDev\Products\Review\UseCase\Average\NewEdit\Tests\NewProductReviewAverageTest;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('products-review')]
final class DeleteProductReviewTest extends KernelTestCase
{
    #[DependsOnClass(EditProductReviewTest::class)]
    #[DependsOnClass(NewProductReviewAverageTest::class)]
    public function testUseCase(): void
    {
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $CurrentEventRepository = self::getContainer()->get(ReviewCurrentEventInterface::class);
        $event = $CurrentEventRepository->get(ProductReviewUid::TEST);

        /** @var EntityManager $EntityManager */
        $productReviewEvent = $EntityManager
            ->getRepository(ProductReviewEvent::class)
            ->findOneBy(['id' => $event]);

        $deleteProductReviewDTO = new DeleteProductReviewDTO();
        $productReviewEvent->getDTO($deleteProductReviewDTO);

        /** @var DeleteProductReviewHandler $DeleteProductReviewHandler */
        $DeleteProductReviewHandler = self::getContainer()->get(DeleteProductReviewHandler::class);
        $handle = $DeleteProductReviewHandler->handle($deleteProductReviewDTO);

        self::assertTrue($handle instanceof ProductReview);
    }

    #[DependsOnClass(EditProductReviewTest::class)]
    #[DependsOnClass(NewProductReviewAverageTest::class)]
    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal->from(ProductReview::class)
            ->where('id = :id')
            ->setParameter('id', ProductReviewUid::TEST);
        self::assertFalse($dbal->fetchExist());
    }

    public static function tearDownAfterClass(): void
    {
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        /** @var EntityManager $EntityManager */
        $main = $EntityManager
            ->getRepository(ProductReview::class)
            ->findOneBy(['id' => ProductReviewUid::TEST]);

        if($main) {
            $EntityManager->remove($main);
        }

        $event = $EntityManager
            ->getRepository(ProductReviewEvent::class)
            ->findBy(['main' => ProductReviewEventUid::TEST]);

        foreach ($event as $remove) {
            $EntityManager->remove($remove);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }
}