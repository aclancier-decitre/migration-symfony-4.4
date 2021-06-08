<?php

namespace App\Form\B2bForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryB2BType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod("POST");
        $builder->setAction($options["action"]);
        $builder->add(
            "id",
            HiddenType::class,
            array(
                "required" => true
            )
        )
            ->add(
                "products",
                CollectionType::class,
                array(
                    "entry_type" => UpdateProductsQuantitiesType::class,
                    "label" => false,
                    'entry_options' => array(
                        'label' => false
                    ),
                )
            )
            ->add(
                "save_change",
                SubmitType::class,
                [
                    "label" => "Enregistrer",
                    "attr" => [
                        "class" => "dct-button dct-button-validate"
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(
            array(
                "data_class" => 'Decitre\Bundle\B2bBundle\Entity\DeliveryB2B'
            )
        );
    }

    public function getBlockPrefix()
    {
        return "delivery_form";
    }
}
