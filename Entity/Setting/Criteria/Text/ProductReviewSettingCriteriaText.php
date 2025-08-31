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

namespace BaksDev\Products\Review\Entity\Setting\Criteria\Text;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Products\Review\Entity\Setting\Criteria\ProductReviewSettingCriteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_setting_criteria_text')]
class ProductReviewSettingCriteriaText extends EntityEvent
{
    /** Связь на событие */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: ProductReviewSettingCriteria::class, inversedBy: 'text')]
    #[ORM\JoinColumn(name: 'criteria', referencedColumnName: 'id')]
    private ProductReviewSettingCriteria $criteria;


    /** Значение свойства */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $value;


    public function __construct(ProductReviewSettingCriteria $criteria)
    {
        $this->criteria = $criteria;
    }

    public function __toString(): string
    {
        return (string) $this->criteria;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /** @return ProductReviewSettingCriteriaTextInterface */
    public function getDto($dto): mixed
    {
        if (is_string($dto) && class_exists($dto)) {
            $dto = new $dto();
        }

        if ($dto instanceof ProductReviewSettingCriteriaTextInterface) {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    /** @var ProductReviewSettingCriteriaTextInterface $dto */
    public function setEntity($dto): mixed
    {
        if ($dto instanceof ProductReviewSettingCriteriaTextInterface) {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
