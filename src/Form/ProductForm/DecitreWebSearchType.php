<?php

namespace App\Form\ProductForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class DecitreWebSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                "url",
                UrlType::class,
                [
                    'required' => true,
                    'constraints' => [new Assert\Url()],
                ]
            )
        ;
    }
}
