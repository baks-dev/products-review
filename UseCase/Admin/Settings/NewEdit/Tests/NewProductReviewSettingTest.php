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
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingUid;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Category\NewEditProductReviewSettingCategoryDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Criteria\NewEditProductReviewSettingCriteriaDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Criteria\Text\NewEditProductReviewSettingCriteriaTextDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\NewEditProductReviewSettingDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\NewEditProductReviewSettingHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('products-review')]
final class NewProductReviewSettingTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(ProductReviewSetting::class)
            ->findOneBy(['id' => ProductReviewSettingUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em->getRepository(ProductReviewSettingEvent::class)
            ->findBy(['main' => ProductReviewSettingUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }

    public function testUseCase(): void
    {
        $NewEditProductReviewSettingHandler = self::getContainer()->get(NewEditProductReviewSettingHandler::class);

        $newEditProductReviewSettingDTO = new NewEditProductReviewSettingDTO();


        /** Добавляем категорию в коллекцию */
        $categoryDTO = new NewEditProductReviewSettingCategoryDTO()->setValue(new CategoryProductUid());
        $newEditProductReviewSettingDTO->addCategory($categoryDTO);

        $criteriaDTO = new NewEditProductReviewSettingCriteriaDTO()
            ->setConst(new ProductReviewSettingCriteriaConst(ProductReviewSettingCriteriaConst::TEST));
        $newEditProductReviewSettingDTO->addCriterion($criteriaDTO);

        /** Добавляем критерий в коллекцию */
        $textDTO = new NewEditProductReviewSettingCriteriaTextDTO()->setValue('Test');

        $criteriaDTO->setText($textDTO);


        /** Второй критерий */

        $criteriaDTO2 = new NewEditProductReviewSettingCriteriaDTO()->setConst(new ProductReviewSettingCriteriaConst());
        $newEditProductReviewSettingDTO->addCriterion($criteriaDTO2);

        /** Добавляем критерий в коллекцию */
        $textDTO2 = new NewEditProductReviewSettingCriteriaTextDTO()->setValue('Test 2');

        $criteriaDTO2->setText($textDTO2);


        /** @var NewEditProductReviewSettingHandler $NewEditProductReviewSettingHandler */
        $handle = $NewEditProductReviewSettingHandler->handle($newEditProductReviewSettingDTO);

        self::assertTrue($handle instanceof ProductReviewSetting);
    }
}