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

namespace BaksDev\Products\Review\Entity\Setting\Criteria;

use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEvent;
use BaksDev\Products\Review\Entity\Setting\Criteria\Text\ProductReviewSettingCriteriaText;
use BaksDev\Products\Review\Type\Setting\Criteria\ConstId\ProductReviewSettingCriteriaConst;
use BaksDev\Products\Review\Type\Setting\Criteria\Id\ProductReviewSettingCriteriaUid;
use Doctrine\ORM\Mapping as ORM;
use BaksDev\Core\Entity\EntityEvent;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_setting_criteria')]
class ProductReviewSettingCriteria extends EntityEvent
{
    /** ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductReviewSettingCriteriaUid::TYPE)]
    private ProductReviewSettingCriteriaUid $id;

    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\ManyToOne(targetEntity: ProductReviewSettingEvent::class, inversedBy: "criteria")]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: "id")]
    private ProductReviewSettingEvent $event;

    /** Постоянный уникальный идентификатор критерия */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductReviewSettingCriteriaConst::TYPE)]
    private readonly ProductReviewSettingCriteriaConst $const;

    /** ProductReviewSettingCriteriaText */
    #[ORM\OneToOne(targetEntity: ProductReviewSettingCriteriaText::class, mappedBy: 'criteria', cascade: ['all'])]
    private ?ProductReviewSettingCriteriaText $text = null;


    public function __construct(ProductReviewSettingEvent $event)
    {
        $this->id = clone new ProductReviewSettingCriteriaUid();
        $this->event = $event;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
    }

    public function getDto($dto): mixed
    {
        if ($dto instanceof ProductReviewSettingCriteriaInterface) {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if ($dto instanceof ProductReviewSettingCriteriaInterface) {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getId(): ProductReviewSettingCriteriaUid
    {
        return $this->id;
    }

    public function getConst(): ProductReviewSettingCriteriaConst
    {
        return $this->const;
    }
}
