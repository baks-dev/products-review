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

namespace BaksDev\Products\Review\UseCase\CurrentUser\Review\NewEdit\Status;

use BaksDev\Products\Review\Entity\Review\Status\ProductReviewStatusInterface;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Products\Review\Type\Status\ReviewStatus\Collection\ReviewStatusSubmit;

final class NewProductReviewStatusDTO implements ProductReviewStatusInterface
{
    /** Status */
    #[Assert\NotBlank]
    private readonly ReviewStatus $value;

    public function __construct()
    {
        $this->value = new ReviewStatus(ReviewStatusSubmit::PARAM);
    }

    public function getValue(): ReviewStatus
    {
        return $this->value;
    }

    public function setValue(ReviewStatus|string $value): self
    {
        if(true === empty($value))
        {
            return $this;
        }

        if(is_string($value))
        {
            $value = new ReviewStatus($value);
        }

        $this->value = $value;
        return $this;
    }
}