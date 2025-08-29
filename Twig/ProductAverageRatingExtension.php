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

namespace BaksDev\Products\Review\Twig;

use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingInterface;
use BaksDev\Products\Review\Repository\AverageProductRating\AverageProductRatingResult;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductAverageRatingExtension extends AbstractExtension
{
    public function __construct(private AverageProductRatingInterface $AverageProductRatingRepository) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'product_average_rating',
                [$this, 'rating'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    public function rating(Environment $twig, ProductUid $product): string
    {
        $result = iterator_to_array($this->AverageProductRatingRepository
            ->product($product)
            ->find());

        $common = 5;

        $avgSum = 0;
        $avgCount = 0;
        foreach($result as $criteria)
        {
            /** @var AverageProductRatingResult $criteria */
            $avgSum += $criteria->getAvg();
            $avgCount += 1;
        }
        if($avgSum > 0 && $avgCount > 0)
        {
            $common = round($avgSum / $avgCount, 2);
        }

        return $twig->render(
            '@products-review/rating/_includes/product.average.rating.html.twig',
            ['common' => $common, 'criteria' => $result],
        );
    }
}