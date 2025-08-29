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

namespace BaksDev\Products\Review\UseCase\Admin\Review\NewEdit;

use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Category\EditProductReviewCategoryDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Name\EditProductReviewNameDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Rating\EditProductReviewRatingDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Status\EditProductReviewStatusDTO;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEventInterface;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Text\EditProductReviewTextDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Product\EditProductReviewProductDTO;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\User\EditProductReviewUserDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Criteria\EditProductReviewCriteriaDTO;

final class EditProductReviewDTO implements ProductReviewEventInterface
{
    /** ID */
    #[Assert\Uuid]
    private ?ProductReviewEventUid $id = null;

    #[Assert\Valid]
    private EditProductReviewCategoryDTO $category;

    #[Assert\Valid]
    private EditProductReviewUserDTO $user;

    #[Assert\Valid]
    private EditProductReviewTextDTO $text;

    #[Assert\Valid]
    private ArrayCollection $criteria;

    #[Assert\Valid]
    private EditProductReviewProductDTO $product;

    private ?EditProductReviewNameDTO $name = null;

    #[Assert\Valid]
    private EditProductReviewStatusDTO $status;

    #[Assert\Valid]
    private EditProductReviewRatingDTO $rating;

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

    public function addCriterion(EditProductReviewCriteriaDTO $criteria): self
    {
        $this->criteria->add($criteria);
        return $this;
    }

    public function removeCriterion(EditProductReviewCriteriaDTO $criteria): self
    {
        $this->criteria->removeElement($criteria);
        return $this;
    }

    public function getUser(): EditProductReviewUserDTO
    {
        return $this->user;
    }

    public function setUser(EditProductReviewUserDTO $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getText(): EditProductReviewTextDTO
    {
        return $this->text;
    }

    public function setText(EditProductReviewTextDTO $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getProduct(): EditProductReviewProductDTO
    {
        return $this->product;
    }

    public function setProduct(EditProductReviewProductDTO $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getName(): ?EditProductReviewNameDTO
    {
        return $this->name;
    }

    public function setName(?EditProductReviewNameDTO $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStatus(): EditProductReviewStatusDTO
    {
        return $this->status;
    }

    public function setStatus(EditProductReviewStatusDTO $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCategory(): EditProductReviewCategoryDTO
    {
        return $this->category;
    }

    public function setCategory(EditProductReviewCategoryDTO $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getId(): ?ProductReviewEventUid
    {
        return $this->id;
    }

    public function setId(?ProductReviewEventUid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getRating(): EditProductReviewRatingDTO
    {
        return $this->rating;
    }

    public function setRating(EditProductReviewRatingDTO $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
}