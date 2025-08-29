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

use BaksDev\Products\Review\Form\Status\ReviewStatusDTO;
use BaksDev\Products\Review\Form\Status\ReviewStatusForm;
use BaksDev\Products\Review\Repository\AllReviews\AllReviewsInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BaksDev\Core\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_PRODUCTS_REVIEW_INDEX')]
final class IndexController extends AbstractController
{
    #[Route('/admin/reviews/{page<\d+>}', name: 'admin.review.index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AllReviewsInterface $allReview,
        int $page = 0,
    ): Response
    {
        // Фильтр по статусам
        $filter = new ReviewStatusDTO();
        $filterForm = $this->createForm(ReviewStatusForm::class, $filter);
        $filterForm->handleRequest($request);


        // Получаем список
        $reviews = $allReview
            ->filter($filter)
            ->findPaginator();

        return $this->render(
            [
                'query' => $reviews,
                'filter' => $filterForm->createView(),
            ]
        );
    }
}