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

namespace BaksDev\Products\Review\Repository\AllReviewSettings;

use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventUid;
use BaksDev\Products\Review\Type\Setting\Id\ProductReviewSettingUid;

final readonly class FindAllReviewSettingsResult
{
    public function __construct(
        private string $review_setting_id,
        private string $review_setting_event,
        private string $categories,
        private ?string $criteria,
    ) {}

    public function getCriteria(): array|false
    {
        if(false === json_validate($this->criteria))
        {
            return false;
        }

        $criteria = json_decode($this->criteria, true, 512, JSON_THROW_ON_ERROR);

        if(false === current($criteria))
        {
            return false;
        }


        return is_array($criteria) ? $criteria : false;
    }

    public function getCategories(): array|false
    {
        if(false === json_validate($this->categories))
        {
            return false;
        }

        $categories = json_decode($this->categories, true, 512, JSON_THROW_ON_ERROR);

        if(false === current($categories))
        {
            return false;
        }

        return $categories;
    }

    public function getReviewSettingEvent(): ProductReviewSettingEventUid|false
    {
        return empty($this->review_setting_event) ? false : new ProductReviewSettingEventUid($this->review_setting_event);
    }

    public function getReviewSettingId(): ProductReviewSettingUid|false
    {
        return empty($this->review_setting_id) ? false : new ProductReviewSettingUid($this->review_setting_id);
    }


}