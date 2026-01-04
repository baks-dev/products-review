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

namespace BaksDev\Products\Review\Type\Status\Tests;

use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\Type\Status\ReviewStatus\ReviewStatusCollection;
use BaksDev\Products\Review\Type\Status\ReviewStatus\ReviewStatusInterface;
use BaksDev\Products\Review\Type\Status\ReviewStatusType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('products-review')]
final class ReviewStatusTest extends KernelTestCase
{
    public function testType(): void
    {
        /** @var ReviewStatusCollection $reviewStatusCollection */
        $reviewStatusCollection = self::getContainer()->get(ReviewStatusCollection::class);

        /** @var ReviewStatusInterface $case */
        foreach($reviewStatusCollection->cases() as $case)
        {
            $reviewStatus = new ReviewStatus($case->getValue());
            self::assertTrue($reviewStatus->equals($case::class)); // немспейс интерфейса
            self::assertTrue($reviewStatus->equals($case)); // объект интерфейса
            self::assertTrue($reviewStatus->equals($case->getValue())); // срока
            self::assertTrue($reviewStatus->equals($reviewStatus)); // объект класса

            $reviewStatusType = new ReviewStatusType();

            $platform = $this
                ->getMockBuilder(AbstractPlatform::class)
                ->getMock();

            $convertToDatabase = $reviewStatusType->convertToDatabaseValue($reviewStatus, $platform);
            self::assertEquals($reviewStatus->getReviewStatusValue(), $convertToDatabase);

            $convertToPHP = $reviewStatusType->convertToPHPValue($convertToDatabase, $platform);
            self::assertInstanceOf(ReviewStatus::class, $convertToPHP);
            self::assertEquals($case, $convertToPHP->getReviewStatus());
        }
    }
}