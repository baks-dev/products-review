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

namespace BaksDev\Products\Review\UseCase\Average\NewEdit;

use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Entity\Average\ProductReviewAverageInterface;
use BaksDev\Products\Review\UseCase\Average\NewEdit\Counter\NewEditProductReviewAverageAverageCounterDTO;
use BaksDev\Products\Review\UseCase\Average\NewEdit\Criteria\NewEditProductReviewAverageCriteriaDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see NewEditProductReviewAverageEvent */
final class NewEditProductReviewAverageDTO implements ProductReviewAverageInterface
{
    /** Product ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private ProductUid $product;

    /** AVG common product rating */
    #[Assert\NotBlank]
    #[Assert\Range(notInRangeMessage: 'Rating must be set in a range from {{ min }} to {{ max }}', min: 1, max: 5)]
    private float $value = 5;

    /** Criteria collection */
    private ArrayCollection $criteria;

    #[Assert\Valid]
    private NewEditProductReviewAverageAverageCounterDTO $counter;

    public function __construct()
    {
        $this->criteria = new ArrayCollection();
    }

    public function getProduct(): ProductUid
    {
        return $this->product;
    }

    public function setProduct(ProductUid $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getCriteria(): ArrayCollection
    {
        return $this->criteria;
    }

    public function setCriteria(ArrayCollection $criteria): self
    {
        $this->criteria = $criteria;
        return $this;
    }

    public function addCriterion(NewEditProductReviewAverageCriteriaDTO $criteria): self
    {
        $this->criteria->add($criteria);
        return $this;
    }

    public function removeCriterion(NewEditProductReviewAverageCriteriaDTO $criteria): self
    {
        $this->criteria->removeElement($criteria);
        return $this;
    }

    public function getCounter(): NewEditProductReviewAverageAverageCounterDTO
    {
        return $this->counter;
    }

    public function setCounter(NewEditProductReviewAverageAverageCounterDTO $counter): self
    {
        $this->counter = $counter;
        return $this;
    }
}