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

namespace BaksDev\Products\Review\Controller\Admin\Settings;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\NewEditProductReviewSettingDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\NewEditProductReviewSettingForm;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\NewEditProductReviewSettingHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use BaksDev\Products\Review\Entity\Setting\ProductReviewSetting;

#[AsController]
#[RoleSecurity('ROLE_PRODUCTS_REVIEW_SETTINGS_NEWEDIT')]
final class NewController extends AbstractController
{
    #[Route('/admin/settings/products/review/new', name: 'admin.settings.newedit.new', methods: ['GET', 'POST'])]
    public function news(
        Request $request,
        NewEditProductReviewSettingHandler $ProductsReviewSettingHandler,
    ): Response {
        $ProductsReviewSettingDTO = new NewEditProductReviewSettingDTO();

        // Форма
        $form = $this->createForm(NewEditProductReviewSettingForm::class, $ProductsReviewSettingDTO, [
            'action' => $this->generateUrl('products-review:admin.settings.newedit.new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->has('new_edit_product_review_setting')) {
            $handle = $ProductsReviewSettingHandler->handle($ProductsReviewSettingDTO);

            $this->addFlash(
                'page.settings.new',
                $handle instanceof ProductReviewSetting ? 'success.settings.new' : 'danger.settings.new',
                'products-review.admin',
                $handle,
            );

            return $handle instanceof ProductReviewSetting ? $this->redirectToRoute('products-review:admin.settings.index') : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
