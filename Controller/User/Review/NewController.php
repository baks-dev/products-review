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

namespace BaksDev\Products\Review\Controller\User\Review;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Orders\Order\Repository\ExistsOrderByProfile\ExistsOrderByProfileInterface;
use BaksDev\Orders\Order\Type\Status\OrderStatus;
use BaksDev\Orders\Order\Type\Status\OrderStatus\Collection\OrderStatusCompleted;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Product\Entity\Product;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\Repository\AllCriteriaByCategory\AllCriteriaByCategoryInterface;
use BaksDev\Products\Review\Repository\AllCriteriaByCategory\AllCriteriaByCategoryResult;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Category\NewProductReviewCategoryDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Criteria\NewProductReviewCriteriaDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Name\NewProductReviewNameDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\NewProductReviewDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\NewProductReviewForm;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\NewProductReviewHandler;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Product\NewProductReviewProductDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Rating\NewProductReviewRatingDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Status\NewProductReviewStatusDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\User\NewProductReviewUserDTO;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileById\UserProfileByIdInterface;
use BaksDev\Users\Profile\UserProfile\Repository\UserProfileById\UserProfileResult;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_PRODUCTS_REVIEW_NEW')]
final class NewController extends AbstractController
{
    #[Route('/product/review/{category}/{product}/new', name: 'user.review.newedit.new', methods: ['GET', 'POST'])]
    public function news(
        Request $request,
        #[MapEntity] Product $product,
        #[MapEntity] CategoryProduct $category,
        UserProfileByIdInterface $UserProfileByIdRepository,
        NewProductReviewHandler $ProductsReviewHandler,
        ExistsOrderByProfileInterface $ExistsOrderByProfileRepository,
        AllCriteriaByCategoryInterface $AllCriteriaByCategoryRepository,
    ): Response
    {
        $exists = $ExistsOrderByProfileRepository->isExist(
            profile: $this->getCurrentProfileUid(),
            status: new OrderStatus(OrderStatusCompleted::STATUS),
            product: $product->getId(),
        );

        if(false === $exists)
        {
            return $this->render(
                true === empty($request->get('offer')) ? [] : [
                    'event' => $request->get('event'),
                    'offer' => $request->get('offer'),
                    'variation' => $request->get('variation'),
                    'modification' => $request->get('modification'),
                ],
                dir: 'user.review.newedit.new.not-available');
        }

        $productsReviewDTO = new NewProductReviewDTO();


        /** Category */
        $productsReviewDTO->setCategory(new NewProductReviewCategoryDTO()->setValue($category->getId()));


        /** Status */
        $productsReviewDTO->setStatus(new NewProductReviewStatusDTO());


        /** Product */
        $productsReviewDTO->setProduct(new NewProductReviewProductDTO()->setValue($product->getId()));


        /** User */
        $userDTO = new NewProductReviewUserDTO()->setValue($this->getCurrentUsr());
        $productsReviewDTO->setUser($userDTO);


        /** Name */
        $profileInfo = $UserProfileByIdRepository->profile($this->getCurrentProfileUid())->find();
        if($profileInfo instanceof UserProfileResult)
        {
            $name = $profileInfo->getContactName();
            if(is_object($name))
            {
                $nameDTO = new NewProductReviewNameDTO()->setValue($name->value);
                $productsReviewDTO->setName($nameDTO);
            }
        }


        /** Criteria */
        $allCriteria = $AllCriteriaByCategoryRepository->category($category->getId())->findAll();

        /** @var AllCriteriaByCategoryResult $criteria */
        foreach($allCriteria as $criteria)
        {
            $criteriaDTO = new NewProductReviewCriteriaDTO()
                ->setCriteria($criteria->getCriteria())
                ->setName($criteria->getName());

            $productsReviewDTO->addCriterion($criteriaDTO);
        }


        // Форма
        $form = $this
            ->createForm(
                NewProductReviewForm::class,
                $productsReviewDTO,
                [
                    'action' => $this->generateUrl(
                        'products-review:user.review.newedit.new',
                        ['product' => $product->getId(), 'category' => $category->getId()],
                    ),
                ])
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('new_product_review'))
        {
            if($form->get('criteria')->count() !== 0)
            {
                /** Расчет общей оценки */
                $ratingSum = 0;
                foreach($form->get('criteria')->getData() as $criterion)
                {
                    /** @var NewProductReviewCriteriaDTO $criterion */
                    $ratingSum += $criterion->getRating();
                }
                $commonRating = round($ratingSum / $form->get('criteria')->count());

                if($commonRating !== 0.0)
                {
                    $ratingDTO = new NewProductReviewRatingDTO()->setValue($commonRating);
                    $productsReviewDTO->setRating($ratingDTO);
                }
            }

            $handle = $ProductsReviewHandler->handle($productsReviewDTO);

            $this->addFlash
            (
                'page.reviews.new',
                $handle instanceof ProductReview ? 'success.new' : 'danger.edit',
                'products-review.user',
                $handle,
            );

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}