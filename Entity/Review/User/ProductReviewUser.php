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

namespace BaksDev\Products\Review\Entity\Review\User;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Products\Review\Entity\Review\Event\ProductReviewEvent;

#[ORM\Entity]
#[ORM\Table(name: 'product_review_user')]
class ProductReviewUser extends EntityEvent
{
    /** Связь на event */
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: ProductReviewEvent::class, inversedBy: 'user')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ProductReviewEvent $event;

    /** ID User  */
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: UserUid::TYPE)]
    private ?UserUid $value;

    public function __construct(ProductReviewEvent $event)
    {
        $this->event = $event;
    }

    public function getUser(): ?UserUid
    {
        return $this->value;
    }

    public function getDto($dto): mixed
    {
        if (is_string($dto) && class_exists($dto)) {
            $dto = new $dto();
        }

        if ($dto instanceof ProductReviewUserInterface) {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if ($dto instanceof ProductReviewUserInterface) {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
