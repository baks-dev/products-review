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

namespace BaksDev\Products\Review\Entity\Review\Rating;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_rating')]
class ProductReviewRating extends EntityEvent
{
    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ProductReviewEvent::class, inversedBy: 'rating')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ProductReviewEvent $event;

    /** Значение оценки */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\Range(
        notInRangeMessage: 'Average rating for all criteria must be set in a range from {{ min }} to {{ max }}',
        min: 1.0,
        max: 5.0
    )]
    private float $value;

    public function __construct(ProductReviewEvent $event)
    {
        $this->event = $event;
    }

    public function getDto($dto): mixed
    {
        if ($dto instanceof ProductReviewRatingInterface) {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if ($dto instanceof ProductReviewRatingInterface) {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}