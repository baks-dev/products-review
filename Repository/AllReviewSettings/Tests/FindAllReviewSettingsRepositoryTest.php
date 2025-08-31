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

namespace BaksDev\Products\Review\Repository\AllReviewSettings\Tests;

use BaksDev\Products\Review\Repository\AllReviewSettings\FindAllReviewSettingsInterface;
use BaksDev\Products\Review\Repository\AllReviewSettings\FindAllReviewSettingsRepository;
use BaksDev\Products\Review\Repository\AllReviewSettings\FindAllReviewSettingsResult;
use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventUid;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingUid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Tests\NewProductReviewSettingTest;

/**
 * @group products-review
 * @group products-review-repository
 *
 * @depends BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Tests\NewProductReviewSettingTest::class
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-repository')]
final class FindAllReviewSettingsRepositoryTest extends KernelTestCase
{
    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public function testFindAll()
    {
        $FindAllReviewSettingsRepository = self::getContainer()->get(FindAllReviewSettingsInterface::class);

        /** @var FindAllReviewSettingsRepository $FindAllReviewSettingsRepository */
        $results = $FindAllReviewSettingsRepository->findAll()->getData();

        foreach ($results as $result)
        {
            self::assertInstanceOf(FindAllReviewSettingsResult::class, $result);

            self::assertTrue(
                $result->getReviewSettingId() === false
                || $result->getReviewSettingId() instanceof ProductReviewSettingUid
            );
            self::assertTrue(
                $result->getReviewSettingEvent() === false
                || $result->getReviewSettingEvent() instanceof ProductReviewSettingEventUid
            );
            self::assertTrue($result->getCategories() === false || is_array($result->getCategories()));
            self::assertTrue($result->getCriteria() === false || is_array($result->getCriteria()));

            return;
        }
    }
}