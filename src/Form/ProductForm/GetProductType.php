<?php

namespace App\Form\ProductForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GetProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('POST');

        $productCodeConstraints = [
            new Length([
                "min" => $options['longueur_ean_min'],
                "max" => $options['longueur_ean_max'],
            ])
        ];

        if ($options['is_product_code_required']) {
            $productCodeConstraints[] = new NotBlank(["message" => "Code produit obligatoire"]);
        }

        $builder->add(
            'ean',
            TextType::class,
            [
                "attr" => [
                    "class" => "trigger-getproduct get-product-ean",
                    "maxlength" => $options['longueur_ean_max'],
                    "data-allow-not-existing-product" => "false",
                    "data-alert-not-existing-editor" => "false",
                ],
                "label" => "EAN",
                "required" => $options['is_product_code_required'],
                "constraints" => $productCodeConstraints,
            ]
        );

        $titleConstraints = [];
        if ($options['is_title_required']) {
            $titleConstraints[] = new NotBlank(["message" => "Produit invalide"]);
        }

        $builder->add(
            'title',
            TextType::class,
            [
                "attr" => [
                    "class" => "data-productinfo get-product-title",
                    "data-productinfo" => "libelle",
                    "readonly" => true,
                ],
                "constraints" => $titleConstraints,
                "label" => $options['title_label'],
                "required" => $options['is_title_required'],
            ]
        );
        if ($options['submit_button']) {
            $builder->add(
                'valid',
                SubmitType::class,
                [
                    'attr' => [
                        "class" => "submit btn dct-button dct-button-validate text-uppercase"
                    ],
                    "label" => "valider",
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'submit_button' => true,
            'is_product_code_required' => true,
            'is_title_required' => true,
            'title_label' => false,
            'longueur_ean_min' => 13,
            'longueur_ean_max' => 13
        ]);
    }
}
