<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Review\Controller\Admin\Settings\Tests;

use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventUid;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Tests\NewProductReviewSettingTest;
use BaksDev\Users\User\Tests\TestUserAccount;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('products-review')]
final class DeleteControllerTest extends WebTestCase
{
    private const string URL = '/admin/settings/products/review/delete/%s';

    private const string ROLE = 'ROLE_PRODUCTS_REVIEW_SETTINGS_DELETE';


    /** Доступ по роли */
    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public function testRoleSuccessful(): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $usr = TestUserAccount::getModer(self::ROLE);

            $client->loginUser($usr, 'user');
            $client->request('GET', sprintf(self::URL, ProductReviewSettingEventUid::TEST));

            self::assertResponseIsSuccessful();
        }

    }

    // доступ по роли ROLE_ADMIN
    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public function testRoleAdminSuccessful(): void
    {

        self::ensureKernelShutdown();
        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $usr = TestUserAccount::getAdmin();

            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->loginUser($usr, 'user');
            $client->request('GET', sprintf(self::URL, ProductReviewSettingEventUid::TEST));

            self::assertResponseIsSuccessful();
        }
    }

    // доступ по роли ROLE_USER
    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public function testRoleUserDeny(): void
    {

        self::ensureKernelShutdown();
        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $usr = TestUserAccount::getUsr();

            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->loginUser($usr, 'user');
            $client->request('GET', sprintf(self::URL, ProductReviewSettingEventUid::TEST));

            self::assertResponseStatusCodeSame(403);
        }

    }

    /** Доступ по без роли */
    #[DependsOnClass(NewProductReviewSettingTest::class)]
    public function testGuestFiled(): void
    {

        self::ensureKernelShutdown();
        $client = self::createClient();

        foreach(TestUserAccount::getDevice() as $device)
        {
            $client->setServerParameter('HTTP_USER_AGENT', $device);
            $client->request('GET', sprintf(self::URL, ProductReviewSettingEventUid::TEST));

            // Full authentication is required to access this resource
            self::assertResponseStatusCodeSame(401);

        }

    }
}
