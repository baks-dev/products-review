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

namespace BaksDev\Products\Review\Type\Average\Criteria\Tests;

use Monolog\Test\TestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use PHPUnit\Framework\Attributes\Group;
use BaksDev\Products\Review\Type\Review\Criteria\Id\ProductReviewCriteriaUid;

/**
 * @group products-review
 * @group products-review-type
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-type')]
final class ProductReviewCriteriaUidTest extends TestCase
{
    public function testConstructor()
    {
        $uid = new ProductReviewCriteriaUid();
        $this->assertInstanceOf(ProductReviewCriteriaUid::class, $uid);

        $value = '0198f0b3-0c6a-7623-ba09-0185852bc6a2';
        $uid = new ProductReviewCriteriaUid($value);
        $this->assertEquals($value, $uid->getValue());
    }

    public function testGetters()
    {
        $attr = 'getAttr';
        $option = 'getOptions';

        $uid = new ProductReviewCriteriaUid(null, $attr, $option);

        $this->assertEquals($attr, $uid->getAttr());
        $this->assertEquals($option, $uid->getOption());
    }
}