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

namespace BaksDev\Products\Review\UseCase\Admin\Settings\Delete\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEvent;
use BaksDev\Products\Review\Entity\Setting\ProductReviewSetting;
use BaksDev\Products\Review\Repository\ReviewSettingCurrentEvent\ReviewSettingCurrentEventInterface;
use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventUid;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingUid;
use BaksDev\Products\Review\UseCase\Admin\Review\Delete\Tests\DeleteProductReviewTest;
use BaksDev\Products\Review\UseCase\Admin\Settings\Delete\DeleteProductReviewSettingDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\Delete\DeleteProductReviewSettingHandler;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Tests\EditProductReviewSettingTest;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('products-review')]
final class DeleteProductReviewSettingTest extends KernelTestCase
{
    #[DependsOnClass(DeleteProductReviewTest::class)]
    #[DependsOnClass(EditProductReviewSettingTest::class)]
    public function testUseCase(): void
    {
        /** @var ReviewSettingCurrentEventInterface $ReviewSettingCurrentEventRepository */
        $ReviewSettingCurrentEventRepository = self::getContainer()->get(ReviewSettingCurrentEventInterface::class);
        $ProductReviewSettingEvent = $ReviewSettingCurrentEventRepository->get(ProductReviewSettingUid::TEST);

        $deleteProductReviewSettingDTO = new DeleteProductReviewSettingDTO();
        $ProductReviewSettingEvent->getDTO($deleteProductReviewSettingDTO);

        /** @var DeleteProductReviewSettingHandler $DeleteProductReviewSettingHandler */
        $DeleteProductReviewSettingHandler = self::getContainer()->get(DeleteProductReviewSettingHandler::class);
        $handle = $DeleteProductReviewSettingHandler->handle($deleteProductReviewSettingDTO);

        self::assertTrue($handle instanceof ProductReviewSetting);
    }

    #[DependsOnClass(DeleteProductReviewTest::class)]
    #[DependsOnClass(EditProductReviewSettingTest::class)]
    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal->from(ProductReviewSetting::class)
            ->where('id = :id')
            ->setParameter('id', ProductReviewSettingUid::TEST);
        self::assertFalse($dbal->fetchExist());
    }

    public static  function tearDownAfterClass(): void
    {
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        /** @var EntityManager $EntityManager */
        $main = $EntityManager
            ->getRepository(ProductReviewSetting::class)
            ->findOneBy(['id' => ProductReviewSettingUid::TEST]);

        if($main) {
            $EntityManager->remove($main);
        }

        $event = $EntityManager
            ->getRepository(ProductReviewSettingEvent::class)
            ->findBy(['main' => ProductReviewSettingEventUid::TEST]);

        foreach ($event as $remove) {
            $EntityManager->remove($remove);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }
}