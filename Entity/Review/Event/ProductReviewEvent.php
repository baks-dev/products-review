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

namespace BaksDev\Products\Review\Entity\Review\Event;

use BaksDev\Products\Review\Entity\Review\Modify\ProductReviewModify;
use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Products\Review\Entity\Review\Product\ProductReviewProduct;
use BaksDev\Products\Review\Entity\Review\ProductReview;
use BaksDev\Products\Review\Entity\Review\Rating\ProductReviewRating;
use BaksDev\Products\Review\Entity\Review\Status\ProductReviewStatus;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Products\Review\Entity\Review\Text\ProductReviewText;
use BaksDev\Products\Review\Entity\Review\User\ProductReviewUser;
use BaksDev\Products\Review\Type\Review\Id\ProductReviewUid;
use BaksDev\Products\Review\Entity\Review\Criteria\ProductReviewCriteria;
use BaksDev\Products\Review\Entity\Review\Name\ProductReviewName;
use Doctrine\Common\Collections\Collection;
use BaksDev\Products\Review\Entity\Review\Category\ProductReviewCategory;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_event')]
class ProductReviewEvent extends EntityEvent
{
    /** ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductReviewEventUid::TYPE)]
    private ProductReviewEventUid $id;

    /** ID ProductReview */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductReviewUid::TYPE, nullable: false)]
    private ?ProductReviewUid $main = null;

    #[Assert\NotBlank]
    #[ORM\OneToOne(targetEntity: ProductReviewUser::class, mappedBy: 'event', cascade: ['all'])]
    private ProductReviewUser $user;

    #[ORM\OneToOne(targetEntity: ProductReviewText::class, mappedBy: 'event', cascade: ['all'])]
    private ProductReviewText $text;

    #[ORM\OneToMany(targetEntity: ProductReviewCriteria::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $criteria;

    #[ORM\OneToOne(targetEntity: ProductReviewProduct::class, mappedBy: 'event', cascade: ['all'])]
    private ProductReviewProduct $product;

    #[ORM\OneToOne(targetEntity: ProductReviewName::class, mappedBy: 'event', cascade: ['all'])]
    private ?ProductReviewName $name = null;

    #[ORM\OneToOne(targetEntity: ProductReviewStatus::class, mappedBy: 'event', cascade: ['all'])]
    private ProductReviewStatus $status;

    #[ORM\OneToOne(targetEntity: ProductReviewCategory::class, mappedBy: 'event', cascade: ['all'])]
    private ProductReviewCategory $category;

    #[ORM\OneToOne(targetEntity: ProductReviewRating::class, mappedBy: 'event', cascade: ['all'])]
    private ?ProductReviewRating $rating = null;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: ProductReviewModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ProductReviewModify $modify;

    public function __construct()
    {
        $this->id = new ProductReviewEventUid();
        $this->criteria = new ArrayCollection();
        $this->modify = new ProductReviewModify($this);
    }

    public function __clone()
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): ProductReviewEventUid
    {
        return $this->id;
    }

    public function setMain(ProductReviewUid|ProductReview $main): void
    {
        $this->main = $main instanceof ProductReview ? $main->getId() : $main;
    }

    public function getMain(): ?ProductReviewUid
    {
        return $this->main;
    }

    public function getStatus(): ProductReviewStatus
    {
        return $this->status;
    }

    public function getProduct(): ProductReviewProduct
    {
        return $this->product;
    }

    public function getDto($dto): mixed
    {
        if ($dto instanceof ProductReviewEventInterface) {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if ($dto instanceof ProductReviewEventInterface) {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
