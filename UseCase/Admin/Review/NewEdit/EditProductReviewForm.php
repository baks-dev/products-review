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

namespace BaksDev\Products\Review\UseCase\Admin\Review\NewEdit;

use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Category\EditProductReviewCategoryForm;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Name\EditProductReviewNameForm;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Status\EditProductReviewStatusForm;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Product\EditProductReviewProductForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Criteria\EditProductReviewCriteriaForm;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\User\EditProductReviewUserForm;
use BaksDev\Products\Review\UseCase\Admin\Review\NewEdit\Text\EditProductReviewTextForm;

final class EditProductReviewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('category', EditProductReviewCategoryForm::class);

        $builder->add('user', EditProductReviewUserForm::class);

        $builder->add('text', EditProductReviewTextForm::class);

        /** CollectionType */
        $builder->add('criteria', CollectionType::class, [
            'entry_type' => EditProductReviewCriteriaForm::class,
            'label' => false,
            'by_reference' => false,
            'allow_delete' => true,
            'allow_add' => true,
            'prototype_name' => '__criteria__',
        ]);

        $builder->add('product', EditProductReviewProductForm::class);

        $builder->add('name', EditProductReviewNameForm::class);

        $builder->add('status', EditProductReviewStatusForm::class);

        /* Сохранить ******************************************************/
        $builder->add(
            'edit_product_review',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']],
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditProductReviewDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}