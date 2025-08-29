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

namespace BaksDev\Products\Review\Entity\Review\Criteria;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use BaksDev\Products\Review\Type\Review\Criteria\Id\ProductReviewCriteriaUid;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_criteria')]
class ProductReviewCriteria extends EntityEvent
{
    /** ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductReviewCriteriaUid::TYPE)]
    private ProductReviewCriteriaUid $id;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductReviewSettingCriteriaConst::TYPE)]
    private ProductReviewSettingCriteriaConst $criteria;

    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\ManyToOne(targetEntity: ProductReviewEvent::class, inversedBy: 'criteria')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ProductReviewEvent $event;

    /** Значение оценки */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Range(notInRangeMessage: 'Rating must be set in a range from {{ min }} to {{ max }}', min: 1, max: 5)]
    private int $rating;

    public function __construct(ProductReviewEvent $event)
    {
        $this->id = clone new ProductReviewCriteriaUid();
        $this->event = $event;
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
    }

    public function getId(): ProductReviewCriteriaUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        if ($dto instanceof ProductReviewCriteriaInterface) {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if(empty($dto->getRating()))
        {
            return false;
        }

        if ($dto instanceof ProductReviewCriteriaInterface) {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}