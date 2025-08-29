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

namespace BaksDev\Products\Review\Repository\ReviewCurrentEvent\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use BaksDev\Products\Review\Repository\ReviewCurrentEvent\ReviewCurrentEventInterface;
use BaksDev\Products\Review\Type\Review\Id\ProductReviewUid;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Tests\NewProductReviewTest;

/**
 * @group products-review
 * @group products-review-repository
 *
 * @depends BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Tests\NewProductReviewTest::class
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-repository')]
final class ReviewCurrentEventTest extends KernelTestCase
{
    #[DependsOnClass(NewProductReviewTest::class)]
    public function testGet()
    {
        $CurrentEventRepository = self::getContainer()->get(ReviewCurrentEventInterface::class);
        $event = $CurrentEventRepository->get(ProductReviewUid::TEST);

        self::assertInstanceOf(ProductReviewEvent::class, $event);
    }
}