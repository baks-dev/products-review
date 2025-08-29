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

namespace BaksDev\Products\Review\Controller\Admin\Review\Tests;

use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Users\User\Tests\TestUserAccount;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Tests\NewProductReviewTest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DependsOnClass;

/**
 * @group products-review
 * @group products-review-controller
 *
 * @group products-review-repository
 * @group products-review-usecase
 *
 * @depends BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Tests\NewProductReviewTest::class
 */
#[When(env: 'test')]
#[Group('products-review')]
#[Group('products-review-controller')]
#[Group('products-review-repository')]
#[Group('products-review-usecase')]
final class DeleteControllerTest extends WebTestCase
{
    private const string URL = '/admin/products/review/delete/';

    private const string ROLE = 'ROLE_PRODUCTS_REVIEW_DELETE';

    /** Доступ по роли */
    #[DependsOnClass(NewProductReviewTest::class)]
    public function testRoleSuccessful(): void
    {
        self::ensureKernelShutdown();

        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $usr = TestUserAccount::getModer(self::ROLE);

            $client->loginUser($usr, 'user');
            $client->request('GET', self::URL.ProductReviewEventUid::TEST);

            self::assertResponseIsSuccessful();
        }

        self::assertTrue(true);
    }

    /** Доступ по роли ROLE_ADMIN */
    #[DependsOnClass(NewProductReviewTest::class)]
    public function testRoleAdminSuccessful(): void
    {
        self::ensureKernelShutdown();

        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $usr = TestUserAccount::getAdmin();

            $client->loginUser($usr, 'user');
            $client->request('GET', self::URL.ProductReviewEventUid::TEST);

            self::assertResponseIsSuccessful();
        }

        self::assertTrue(true);
    }

    /** Доступ по роли ROLE_USER */
    #[DependsOnClass(NewProductReviewTest::class)]
    public function testRoleUserFiled(): void
    {
        self::ensureKernelShutdown();

        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);

            $usr = TestUserAccount::getUsr();
            $client->loginUser($usr, 'user');
            $client->request('GET', self::URL.ProductReviewEventUid::TEST);

            self::assertResponseStatusCodeSame(403);
        }

        self::assertTrue(true);
    }

    /** Доступ без роли */
    #[DependsOnClass(NewProductReviewTest::class)]
    public function testGuestFiled(): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->request('GET', self::URL.ProductReviewEventUid::TEST);

            self::assertResponseStatusCodeSame(401);
        }

        self::assertTrue(true);
    }
}