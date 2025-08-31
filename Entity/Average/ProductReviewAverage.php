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

namespace BaksDev\Products\Review\Entity\Average;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Entity\Average\Counter\ProductReviewAverageCounter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;
use BaksDev\Products\Review\Entity\Average\Criteria\ProductReviewAverageCriteria;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_average')]
class ProductReviewAverage extends EntityState
{
    /** Product ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductUid::TYPE)]
    private ProductUid $product;

    /** AVG common product rating */
    #[Assert\NotBlank]
    #[Assert\Range(notInRangeMessage: 'Rating must be set in a range from {{ min }} to {{ max }}', min: 1, max: 5)]
    #[ORM\Column(type: Types::FLOAT)]
    private float $value;

    /** Criteria collection */
    #[ORM\OneToMany(targetEntity: ProductReviewAverageCriteria::class, mappedBy: 'average', cascade: ['all'])]
    private Collection $criteria;

     #[ORM\OneToOne(targetEntity: ProductReviewAverageCounter::class, mappedBy: 'average', cascade: ['all'])]
     private ?ProductReviewAverageCounter $counter = null;

    public function __construct(ProductUid $product)
    {
        $this->product = $product;
        $this->criteria = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->product;
    }

    public function getId(): ProductUid
    {
        return $this->product;
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

    /** Гидрирует переданную DTO, вызывая ее сеттеры */
    public function getDto($dto): mixed
    {
        if($dto instanceof ProductReviewAverageInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    /** Гидрирует сущность переданной DTO */
    public function setEntity($dto): mixed
    {
        if($dto instanceof ProductReviewAverageInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getCriteria(): Collection
    {
        return $this->criteria;
    }
}