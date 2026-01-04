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

namespace BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Tests;

use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEvent;
use BaksDev\Products\Review\Entity\Setting\ProductReviewSetting;
use BaksDev\Products\Review\Repository\ReviewSettingCurrentEvent\ReviewSettingCurrentEventInterface;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingUid;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Category\NewEditProductReviewSettingCategoryDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Criteria\NewEditProductReviewSettingCriteriaDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Criteria\Text\NewEditProductReviewSettingCriteriaTextDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\NewEditProductReviewSettingDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\NewEditProductReviewSettingHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('products-review')]
final class EditProductReviewSettingTest extends KernelTestCase
{
    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public function testUseCase(): void
    {

        /** @var ReviewSettingCurrentEventInterface $ReviewSettingCurrentEventRepository */
        $ReviewSettingCurrentEventRepository = self::getContainer()->get(ReviewSettingCurrentEventInterface::class);
        $ProductReviewSettingEvent = $ReviewSettingCurrentEventRepository->get(ProductReviewSettingUid::TEST);
        self::assertInstanceOf(ProductReviewSettingEvent::class, $ProductReviewSettingEvent);


        $newEditProductReviewSettingDTO = new NewEditProductReviewSettingDTO();
        $ProductReviewSettingEvent->getDto($newEditProductReviewSettingDTO);

        $NewEditProductReviewSettingCriteriaDTO = $newEditProductReviewSettingDTO->getCriteria()->current();

        /** Проверяем наличие первого критерия */
        self::assertInstanceOf(
            NewEditProductReviewSettingCriteriaDTO::class,
            $NewEditProductReviewSettingCriteriaDTO,
        );

        self::assertInstanceOf(
            NewEditProductReviewSettingCriteriaTextDTO::class,
            $NewEditProductReviewSettingCriteriaDTO->getText(),
        );

        self::assertInstanceOf(
            ProductReviewSettingCriteriaConst::class,
            $NewEditProductReviewSettingCriteriaDTO->getConst(),
        );

        self::assertEquals('Test', $NewEditProductReviewSettingCriteriaDTO->getText()->getValue());


        /** Проверяем наличие первой категории */
        self::assertInstanceOf(
            NewEditProductReviewSettingCategoryDTO::class,
            $newEditProductReviewSettingDTO->getCategory()->current(),
        );

        /** Добавляем новую категорию в коллекцию */
        $categoryDTO = new NewEditProductReviewSettingCategoryDTO()->setValue(clone new CategoryProductUid());
        $newEditProductReviewSettingDTO->addCategory($categoryDTO);


        /** Добавляем новый критерий в коллекцию */
        $criteriaDTO = new NewEditProductReviewSettingCriteriaDTO()->setConst(clone new ProductReviewSettingCriteriaConst());
        $newEditProductReviewSettingDTO->addCriterion($criteriaDTO);

        $textDTO = new NewEditProductReviewSettingCriteriaTextDTO()->setValue('Test3');

        $criteriaDTO->setText($textDTO);


        /** @var NewEditProductReviewSettingHandler $NewEditProductReviewSettingHandler */
        $NewEditProductReviewSettingHandler = self::getContainer()->get(NewEditProductReviewSettingHandler::class);
        $handle = $NewEditProductReviewSettingHandler->handle($newEditProductReviewSettingDTO);

        self::assertTrue($handle instanceof ProductReviewSetting);
    }
}