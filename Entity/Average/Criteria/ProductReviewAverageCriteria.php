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

namespace BaksDev\Products\Review\Entity\Average\Criteria;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Products\Review\Entity\Average\ProductReviewAverage;
use BaksDev\Products\Review\Type\Average\Criteria\ProductReviewAverageCriteriaUid;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use BaksDev\Products\Review\Type\Setting\Criteria\Id\ProductReviewSettingCriteriaUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_average_criteria')]
class ProductReviewAverageCriteria extends EntityState
{
    /** Average Criteria ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductReviewAverageCriteriaUid::TYPE)]
    private readonly ProductReviewAverageCriteriaUid $id;

    /** Product ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\ManyToOne(targetEntity: ProductReviewAverage::class, inversedBy: 'criteria')]
    #[ORM\JoinColumn(name: 'product', referencedColumnName: 'product')]
    private ProductReviewAverage $average;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductReviewSettingCriteriaConst::TYPE)]
    private ProductReviewSettingCriteriaConst $criteria;

    #[Assert\NotBlank]
    #[Assert\Range(notInRangeMessage: 'Rating must be set in a range from {{ min }} to {{ max }}', min: 1, max: 5)]
    #[ORM\Column(type: Types::FLOAT)]
    private float $value;

    public function __construct(ProductReviewAverage $average)
    {
        $this->id = clone(new ProductReviewAverageCriteriaUid());
        $this->average = $average;
    }

    public function getId(): ProductReviewAverageCriteriaUid
    {
        return $this->id;
    }
    
    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof ProductReviewAverageCriteriaInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof ProductReviewAverageCriteriaInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}