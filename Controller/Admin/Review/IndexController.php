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
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Products\Review\Form\ReviewFilter\Admin\ProductReviewFilterDTO;
use BaksDev\Products\Review\Form\ReviewFilter\Admin\ProductReviewFilterForm;
use BaksDev\Products\Review\Repository\AllReviews\AllReviewsInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

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

        /* Поиск */
        $search = new SearchDTO();

        $searchForm = $this
            ->createForm(
                type: SearchForm::class,
                data: $search,
                options: ['action' => $this->generateUrl('products-review:admin.review.index')],
            )
            ->handleRequest($request);


        /* Фильтр */
        $filter = new ProductReviewFilterDTO();

        $filterForm = $this
            ->createForm(
                type: ProductReviewFilterForm::class,
                data: $filter,
                options: ['action' => $this->generateUrl('products-review:admin.review.index')],
            )
            ->handleRequest($request);


        /* Получаем список */
        $reviews = $allReview
            ->search($search)
            ->filter($filter)
            ->setAllProjects(true) // Выводить отзывы со всех профилей/складов
            ->setActive(false) // По умолчанию выводить все отзывы
            ->findPaginator();

        return $this->render(
            [
                'search' => $searchForm->createView(),
                'query' => $reviews,
                'filter' => $filterForm->createView(),
            ],
        );
    }
}