<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
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
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Products\Review\Form\Reviews\Status\ChangeStatusSelectedReviewsDTO;
use BaksDev\Products\Review\Form\Reviews\Status\ChangeStatusSelectedReviewsForm;
use BaksDev\Products\Review\Messenger\Status\ChangeStatusProductReviewMessage;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Products\Review\Type\Status\ReviewStatus\Collection\ReviewStatusActive;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_PRODUCTS_REVIEW_EDIT')]
final class ActiveController extends AbstractController
{
    #[Route('/admin/products/review/status/active', name: 'admin.review.newedit.status.active', methods: ['GET', 'POST'])]
    public function active(Request $request, MessageDispatchInterface $MessageDispatch): Response
    {
        $ChangeStatusSelectedReviewsDTO = new ChangeStatusSelectedReviewsDTO();

        $form = $this
            ->createForm(
                ChangeStatusSelectedReviewsForm::class,
                $ChangeStatusSelectedReviewsDTO,
                ['action' => $this->generateUrl('products-review:admin.review.newedit.status.active')]
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('change_status_selected_reviews'))
        {
            foreach($ChangeStatusSelectedReviewsDTO->getCollection() as $ChangeStatusSelectedReviewDTO)
            {
                $ChangeStatusReviewMessage = new ChangeStatusProductReviewMessage()
                    ->setEvent($ChangeStatusSelectedReviewDTO->getId())
                    ->setStatus(new ReviewStatus(ReviewStatusActive::class));

                $MessageDispatch->dispatch($ChangeStatusReviewMessage, transport: 'products-review');
            }

            $this->addFlash
            (
                'page.reviews.status',
                'success.reviews.status',
                'products-review.admin',
            );

            return $this->redirectToRoute('products-review:admin.review.index');
        }

        return $this->render(['form' => $form->createView()]);
    }
}