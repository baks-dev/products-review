<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Type;

use BaksDev\Products\Review\Entity\Review\Type\ProductReviewType;
use BaksDev\Products\Review\Entity\Review\Type\ProductReviewTypeInterface;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ProductReviewType */
final class NewProductReviewTypeDTO implements ProductReviewTypeInterface
{

    /** Type */
    #[Assert\NotBlank]
    private TypeProfileUid $type;


    /** Идентификатор токена маркетплейса */
    #[Assert\NotBlank]
    private Uuid $token;

    /** Внешний идентификатор сообщения/отзыва */
    private string $external;

    public function getExternal(): string
    {
        return $this->external;
    }

    public function setExternal(string|int $external): self
    {
        $this->external = (string) $external;

        return $this;
    }


    public function getType(): TypeProfileUid
    {
        return $this->type;
    }

    public function setType(TypeProfileUid $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getToken(): Uuid
    {
        return $this->token;
    }

    public function setToken(Uuid $token): self
    {
        $this->token = $token;

        return $this;
    }

}