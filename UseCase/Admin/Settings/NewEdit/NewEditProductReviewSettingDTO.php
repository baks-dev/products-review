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

namespace BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit;

use BaksDev\Products\Review\Entity\Setting\Event\ProductReviewSettingEventInterface;
use BaksDev\Products\Review\Type\Setting\Event\ProductReviewSettingEventUid;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Category\NewEditProductReviewSettingCategoryDTO;
use BaksDev\Products\Review\UseCase\Admin\Settings\NewEdit\Criteria\NewEditProductReviewSettingCriteriaDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ProductReviewSettingEvent */
final class NewEditProductReviewSettingDTO implements ProductReviewSettingEventInterface
{
    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?ProductReviewSettingEventUid $id = null;

    private ArrayCollection $category;

    private ArrayCollection $criteria;


    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->criteria = new ArrayCollection();
    }

    /**
     * Идентификатор события
     */
    public function getEvent(): ?ProductReviewSettingEventUid
    {
        return $this->id;
    }

    public function setEvent(ProductReviewSettingEventUid $id): self
    {
        $this->id = $id;
        return $this;
    }


    /** Category */
    public function getCategory(): ArrayCollection
    {
        if ($this->category->isEmpty()) {
            $ProductReviewSettingCategoryDTO = new NewEditProductReviewSettingCategoryDTO();
            $this->addCategory($ProductReviewSettingCategoryDTO);
        }

        return $this->category;
    }

    public function addCategory(NewEditProductReviewSettingCategoryDTO $category): self
    {
        /**
         * Делаем проверку, чтобы не добавлять одинаковые категории в коллекцию (т.к. нет подобных ограничений на ui
         */
        $filter = $this->category->filter(
            function(NewEditProductReviewSettingCategoryDTO $element) use ($category): bool
            {
                return $element->getValue()?->equals($category->getValue());
            }
        );

        if($filter->isEmpty())
        {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(NewEditProductReviewSettingCategoryDTO $category): void
    {
        $this->category->removeElement($category);
    }


    public function getCriteria(): ArrayCollection
    {
        return $this->criteria;
    }

    public function addCriterion(NewEditProductReviewSettingCriteriaDTO $criteria): self
    {
        $this->criteria->add($criteria);
        return $this;
    }

    public function removeCriterion(NewEditProductReviewSettingCriteriaDTO $criteria): void
    {
        $this->criteria->removeElement($criteria);
    }
}
