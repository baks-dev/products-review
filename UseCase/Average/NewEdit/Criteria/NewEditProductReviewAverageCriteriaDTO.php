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

namespace BaksDev\Products\Review\UseCase\Average\NewEdit\Criteria;

use BaksDev\Products\Review\Entity\Average\Criteria\ProductReviewAverageCriteriaInterface;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use Symfony\Component\Validator\Constraints as Assert;

final class NewEditProductReviewAverageCriteriaDTO implements ProductReviewAverageCriteriaInterface
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private ProductReviewSettingCriteriaConst $criteria;

    #[Assert\NotBlank]
    #[Assert\Range(notInRangeMessage: 'Rating must be set in a range from {{ min }} to {{ max }}', min: 1, max: 5)]
    private float $value;

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getCriteria(): ProductReviewSettingCriteriaConst
    {
        return $this->criteria;
    }

    public function setCriteria(ProductReviewSettingCriteriaConst $criteria): self
    {
        $this->criteria = $criteria;
        return $this;
    }
}