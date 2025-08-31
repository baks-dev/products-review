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

namespace BaksDev\Products\Review\Repository\AllReviews;

use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Type\Review\Event\ProductReviewEventUid;
use BaksDev\Products\Review\Type\Status\ReviewStatus;
use BaksDev\Users\User\Type\Id\UserUid;
use DateTimeImmutable;

final readonly class AllReviewsResult
{
    public function __construct(
        private string $review_event,
        private string $update,
        private string $review_product,
        private string $product_name,
        private string $review_status,
        private string $review_text,
        private string $review_user,
        private string $profile_username,
        private ?float $review_rating_value,
        private ?string $review_name,
    ) {}

    public function getReviewEvent(): ProductReviewEventUid
    {
        return new ProductReviewEventUid($this->review_event);
    }

    public function getReviewProduct(): ProductUid
    {
        return new ProductUid($this->review_product);
    }

    public function getProductName(): string
    {
        return $this->product_name;
    }

    public function getReviewStatus(): ReviewStatus
    {
        return new ReviewStatus($this->review_status);
    }

    public function getReviewText(): string
    {
        return $this->review_text;
    }

    public function getReviewUser(): UserUid
    {
        return new UserUid($this->review_user);
    }

    public function getReviewName(): ?string
    {
        return $this->review_name;
    }

    public function getProfileUsername(): string
    {
        return $this->profile_username;
    }

    public function getUpdate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->update);
    }

    public function getReviewRatingValue(): ?float
    {
        return $this->review_rating_value;
    }
}