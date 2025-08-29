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

namespace BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Tests;

use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Review\Repository\ReviewSettingCurrentEvent\ReviewSettingCurrentEventInterface;
use BaksDev\Products\Review\Repository\ReviewSettingCurrentEvent\ReviewSettingCurrentEventRepository;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingUid;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Name\NewProductReviewNameDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Rating\NewProductReviewRatingDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Status\NewProductReviewStatusDTO;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\User\NewProductReviewUserDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\NewProductReviewHandler;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Product\NewProductReviewProductDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Text\NewProductReviewTextDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Attribute\When;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\NewProductReviewDTO;
use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Users\User\Type\Id\UserUid;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Criteria\NewProductReviewCriteriaDTO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use BaksDev\Products\Review\Type\Review\Id\ProductReviewUid;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Category\NewProductReviewCategoryDTO;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Tests\NewProductReviewSettingTest;

/**
 * @group products-review
 * @group products-review-usecase
 *
 * @group products-review-controller
 * @group products-review-repository
 *
 * @depends BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Tests\NewProductReviewSettingTest::class
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-usecase')]
#[Group('products-review-controller')]
#[Group('products-review-repository')]
final class NewProductReviewTest extends KernelTestCase
{
    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public static function setUpBeforeClass(): void
    {
        // Бросаем событие консольной команды
        $dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
        $event = new ConsoleCommandEvent(new Command(), new StringInput(''), new NullOutput());
        $dispatcher->dispatch($event, 'console.command');

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em
            ->getRepository(ProductReview::class)
            ->findOneBy(['id' => ProductReviewUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em
            ->getRepository(ProductReviewEvent::class)
            ->findBy(['main' => ProductReviewUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }

    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public function testUseCase(): void
    {
        $NewEditProductReviewHandler = self::getContainer()->get(NewProductReviewHandler::class);

        $newEditProductReviewDTO = new NewProductReviewDTO();


        /** Получаем существующий критерий (важно для теста AverageProductRatingResult) */
        $ReviewSettingCurrentEventRepository = self::getContainer()->get(ReviewSettingCurrentEventInterface::class);
        /** @var ReviewSettingCurrentEventRepository $ReviewSettingCurrentEventRepository */
        $settingEvent = $ReviewSettingCurrentEventRepository->get(ProductReviewSettingUid::TEST);
        $criteria = $settingEvent->getCriteria()->current()->getConst();

        /** Добавляем критерий в коллекцию */
        $criteriaDTO = new NewProductReviewCriteriaDTO()
            ->setCriteria($criteria)
            ->setRating(5);

        $newEditProductReviewDTO->addCriterion($criteriaDTO);


        /** Добавляем категорию */
        $categoryDTO = new NewProductReviewCategoryDTO()->setValue(new CategoryProductUid());

        $newEditProductReviewDTO->setCategory($categoryDTO);

        /** Добаляем имя */
        $nameDTO = new NewProductReviewNameDTO()->setValue('Test');

        $newEditProductReviewDTO->setName($nameDTO);

        /** Добавляем продукт */
        $productDTO = new NewProductReviewProductDTO()->setValue(new ProductUid());

        $newEditProductReviewDTO->setProduct($productDTO);


        /** Добавляем статус */
        $statusDTO = new NewProductReviewStatusDTO();

        $newEditProductReviewDTO->setStatus($statusDTO);


        /** Добавляем текст */
        $textDTO = new NewProductReviewTextDTO()->setValue('Test');

        $newEditProductReviewDTO->setText($textDTO);


        /** Добавляем пользователя */
        $userDTO = new NewProductReviewUserDTO()->setValue(UserUid::TEST);

        $newEditProductReviewDTO->setUser($userDTO);


        /** Добавляем общую оценку */
        $ratingDTO = new NewProductReviewRatingDTO()->setValue(5);

        $newEditProductReviewDTO->setRating($ratingDTO);


        /** @var NewProductReviewHandler $NewEditProductReviewHandler */
        $handle = $NewEditProductReviewHandler->handle($newEditProductReviewDTO);

        self::assertInstanceOf(ProductReview::class, $handle);
    }
}