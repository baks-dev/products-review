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

namespace BaksDev\Products\Review\Controller\Admin\Review;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\UseCase\Admin\Review\Delete\DeleteProductReviewDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\Delete\DeleteProductReviewForm;
use BaksDev\Products\Review\UseCase\Admin\Review\Delete\DeleteProductReviewHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_PRODUCTS_REVIEW_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/products/review/delete/{id}', name: 'admin.review.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] ProductReviewEvent $ProductsReviewEvent,
        DeleteProductReviewHandler $ProductsReviewDeleteHandler,
    ): Response
    {
        $ProductsReviewDeleteDTO = new DeleteProductReviewDTO();
        $ProductsReviewEvent->getDto($ProductsReviewDeleteDTO);

        $form = $this->createForm(DeleteProductReviewForm::class, $ProductsReviewDeleteDTO, [
            'action' => $this->generateUrl(
                'products-review:admin.review.delete',
                ['id' => $ProductsReviewEvent]
            ),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('delete_product_review'))
        {
            $handle = $ProductsReviewDeleteHandler->handle($ProductsReviewDeleteDTO);

            $this->addFlash
            (
                'page.reviews.delete',
                $handle instanceof ProductReview ? 'success.reviews.delete' : 'danger.reviews.delete',
                'products-review.admin',
                $handle
            );

            return $handle instanceof ProductReview ? $this->redirectToRoute('products-review:admin.review.index') : $this->redirectToReferer();
        }

        return $this->render([
            'form' => $form->createView(),
        ]);
    }
}