<?php

declare(strict_types=1);

namespace BaksDev\Products\Review\Entity\Average\Counter;

interface ProductReviewAverageCounterInterface
{
    /**
     * Значение свойства
     *
     * @see ProductReviewAverageCounter
     */
    public function getValue(): ?int;
}