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

namespace BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Tests;

use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Name\EditProductReviewNameDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\EditProductReviewDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Product\EditProductReviewProductDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Rating\EditProductReviewRatingDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Status\EditProductReviewStatusDTO;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\EditProductReviewHandler;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Criteria\EditProductReviewCriteriaDTO;
use BaksDev\Products\Review\Type\Status\ReviewStatus\Collection\ReviewStatusActive;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Text\EditProductReviewTextDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\User\EditProductReviewUserDTO;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use BaksDev\Products\Review\Controller\Admin\Review\Tests\EditControllerTest;
use BaksDev\Products\Review\Controller\Admin\Review\Tests\DeleteControllerTest;

/**
 * @group products-review
 * @group products-review-usecase
 *
 * @group products-review-repository
 * @group products-review-controller
 *
 * @depends BaksDev\Products\Review\Controller\Admin\Review\Tests\EditControllerTest::class
 * @depends BaksDev\Products\Review\Controller\Admin\Review\Tests\DeleteControllerTest::class
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-usecase')]
#[Group('products-review-repository')]
#[Group('products-review-controller')]
final class EditProductReviewTest extends KernelTestCase
{
    #[DependsOnClass(EditControllerTest::class)]
    #[DependsOnClass(DeleteControllerTest::class)]
    public function testUseCase(): void
    {
        $NewEditProductReviewHandler = self::getContainer()->get(EditProductReviewHandler::class);

        $newEditProductReviewDTO = new EditProductReviewDTO();

        $em = self::getContainer()->get(EntityManagerInterface::class);

        /** @var EntityManager $em */
        $productReviewEvent = $em
            ->getRepository(ProductReviewEvent::class)
            ->findOneBy(['id' => ProductReviewEventUid::TEST]);

        $productReviewEvent->getDto($newEditProductReviewDTO);


        /** Criteria */
        $newEditProductReviewCriteriaDTO = $newEditProductReviewDTO->getCriteria()->current();

        /** Проверка наличия критерия */
        self::assertInstanceOf(EditProductReviewCriteriaDTO::class, $newEditProductReviewCriteriaDTO);
        self::assertInstanceOf(
            ProductReviewSettingCriteriaConst::class,
            $newEditProductReviewCriteriaDTO->getCriteria()
        );
        self::assertIsInt($newEditProductReviewCriteriaDTO->getRating());

        /** Устанавливаем второй критерий */
        $newEditProductReviewCriteriaDTO2 = new EditProductReviewCriteriaDTO();
        $newEditProductReviewCriteriaDTO2->setCriteria(
            new ProductReviewSettingCriteriaConst()
        );

        $newEditProductReviewCriteriaDTO2->setRating(2);
        $newEditProductReviewDTO->addCriterion($newEditProductReviewCriteriaDTO2);

        /** Name */
        $newEditProductReviewNameDTO = $newEditProductReviewDTO->getName();

        /** Проверка наличия имени */
        self::assertInstanceOf(EditProductReviewNameDTO::class, $newEditProductReviewNameDTO);
        self::assertIsString($newEditProductReviewNameDTO->getValue());

        /** Изменяем имя */
        $newEditProductReviewNameDTO->setValue('Test2');
        $newEditProductReviewDTO->setName($newEditProductReviewNameDTO);


        /** Product */
        $newEditProductReviewProductDTO = $newEditProductReviewDTO->getProduct();

        /** Проверка наличия продукта */
        self::assertInstanceOf(EditProductReviewProductDTO::class, $newEditProductReviewProductDTO);
        self::assertInstanceOf(ProductUid::class, $newEditProductReviewProductDTO->getValue());

        /** Изменяем продукт */
        $newEditProductReviewProductDTO->setValue(new ProductUid());
        $newEditProductReviewDTO->setProduct($newEditProductReviewProductDTO);


        /** Status */
        $newEditProductReviewStatusDTO = $newEditProductReviewDTO->getStatus();

        /** Проверка наличия статуса */
        self::assertInstanceOf(EditProductReviewStatusDTO::class, $newEditProductReviewStatusDTO);
        self::assertInstanceOf(ReviewStatus::class, $newEditProductReviewStatusDTO->getValue());

        /** Изменяем статус */
        $newEditProductReviewStatusDTO->setValue(new ReviewStatus(ReviewStatusActive::class));
        $newEditProductReviewDTO->setStatus($newEditProductReviewStatusDTO);


        /** Text */
        $newEditProductReviewTextDTO = $newEditProductReviewDTO->getText();

        /** Проверка наличия текста */
        self::assertInstanceOf(EditProductReviewTextDTO::class, $newEditProductReviewTextDTO);
        self::assertInstanceOf(ReviewStatus::class, $newEditProductReviewStatusDTO->getValue());

        /** Изменяем текст */
        $newEditProductReviewTextDTO->setValue('Test2');
        $newEditProductReviewDTO->setText($newEditProductReviewTextDTO);


        /** User */
        $newEditProductReviewUserDTO = $newEditProductReviewDTO->getUser();

        /** Проверка наличия пользователя */
        self::assertInstanceOf(EditProductReviewUserDTO::class, $newEditProductReviewUserDTO);
        self::assertInstanceOf(UserUid::class, $newEditProductReviewUserDTO->getValue());

        /** Изменяем пользователя */
        $newEditProductReviewUserDTO->setValue(new UserUid());
        $newEditProductReviewDTO->setUser($newEditProductReviewUserDTO);


        /** Rating */
        $newEditProductReviewRatingDTO = $newEditProductReviewDTO->getRating();

        /** Проверка наличия общей оценки */
        self::assertInstanceOf(EditProductReviewRatingDTO::class, $newEditProductReviewRatingDTO);
        self::assertIsFloat($newEditProductReviewRatingDTO->getValue());

        /** Изменяем общую оценку */
        $newEditProductReviewRatingDTO->setValue(3,5);
        $newEditProductReviewDTO->setRating($newEditProductReviewRatingDTO);

        $handle = $NewEditProductReviewHandler->handle($newEditProductReviewDTO);
        self::assertInstanceOf(ProductReview::class, $handle);
    }
}