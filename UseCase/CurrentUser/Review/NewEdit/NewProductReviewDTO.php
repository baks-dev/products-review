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

namespace BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit;

use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Category\NewProductReviewCategoryDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Name\NewProductReviewNameDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Rating\NewProductReviewRatingDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Status\NewProductReviewStatusDTO;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEventInterface;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Text\NewProductReviewTextDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Product\NewProductReviewProductDTO;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\User\NewProductReviewUserDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Criteria\NewProductReviewCriteriaDTO;

final class NewProductReviewDTO implements ProductReviewEventInterface
{
    /** ID */
    #[Assert\Uuid]
    private ?ProductReviewEventUid $id = null;

    #[Assert\Valid]
    private NewProductReviewCategoryDTO $category;

    #[Assert\Valid]
    private NewProductReviewUserDTO $user;

    #[Assert\Valid]
    private NewProductReviewTextDTO $text;

    private ArrayCollection $criteria;

    #[Assert\Valid]
    private NewProductReviewProductDTO $product;

    private ?NewProductReviewNameDTO $name = null;

    #[Assert\Valid]
    private NewProductReviewStatusDTO $status;

    #[Assert\Valid]
    private NewProductReviewRatingDTO $rating;

    public function __construct()
    {
        $this->criteria = new ArrayCollection();
    }

    /**
     * Идентификатор события
     */
    public function getEvent(): ?ProductReviewEventUid
    {
        return $this->id;
    }

    public function setEvent(ProductReviewEventUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCriteria(): ArrayCollection
    {
        return $this->criteria;
    }

    public function addCriterion(NewProductReviewCriteriaDTO $criteria): self
    {
        $this->criteria->add($criteria);
        return $this;
    }

    public function removeCriterion(NewProductReviewCriteriaDTO $criteria): void
    {
        $this->criteria->removeElement($criteria);
    }

    public function getId(): ?ProductReviewEventUid
    {
        return $this->id;
    }

    public function setId(ProductReviewEventUid $id): void
    {
        $this->id = $id;
    }

    public function getUser(): NewProductReviewUserDTO
    {
        return $this->user;
    }

    public function setUser(NewProductReviewUserDTO $user): void
    {
        $this->user = $user;
    }

    public function getText(): NewProductReviewTextDTO
    {
        return $this->text;
    }

    public function setText(NewProductReviewTextDTO $text): void
    {
        $this->text = $text;
    }

    public function getProduct(): NewProductReviewProductDTO
    {
        return $this->product;
    }

    public function setProduct(NewProductReviewProductDTO $product): void
    {
        $this->product = $product;
    }

    public function getName(): ?NewProductReviewNameDTO
    {
        return $this->name;
    }

    public function setName(?NewProductReviewNameDTO $name): void
    {
        $this->name = $name;
    }

    public function getStatus(): NewProductReviewStatusDTO
    {
        return $this->status;
    }

    public function setStatus(NewProductReviewStatusDTO $status): void
    {
        $this->status = $status;
    }

    public function setCriteria(ArrayCollection $criteria): void
    {
        $this->criteria = $criteria;
    }

    public function getCategory(): NewProductReviewCategoryDTO
    {
        return $this->category;
    }

    public function setCategory(NewProductReviewCategoryDTO $category): void
    {
        $this->category = $category;
    }

    public function getRating(): NewProductReviewRatingDTO
    {
        return $this->rating;
    }

    public function setRating(NewProductReviewRatingDTO $rating): void
    {
        $this->rating = $rating;
    }

}