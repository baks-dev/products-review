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

namespace BaksDev\Products\Review\Entity\Review;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Products\Review\Type\Review\Id\ProductReviewUid;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;

#[ORM\Entity]
#[ORM\Table(name: 'product_review')]
class ProductReview
{
    /** ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductReviewUid::TYPE)]
    private ProductReviewUid $id;

    /** ID События */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductReviewEventUid::TYPE, unique: true)]
    private ProductReviewEventUid $event;


    public function __construct()
    {
        $this->id = new ProductReviewUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getEvent(): ProductReviewEventUid
    {
        return $this->event;
    }

    public function setEvent(ProductReviewEventUid|ProductReviewEvent $event): void
    {
        $this->event = $event instanceof ProductReviewEvent ? $event->getId() : $event;
    }

    public function getId(): ProductReviewUid
    {
        return $this->id;
    }
}
