<?php

namespace App\Form\B2bForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class UpdateProductsQuantitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod("POST");
        $builder->add(
            "lineNumber",
            HiddenType::class,
            array(
                "label" => false,
                "required" => false,
            )
        )->add(
            "deliveredQuantity",
            IntegerType::class,
            array(
                "required" => true,
                "constraints" => array(
                    new Range(
                        array(
                            "min" => 0,
                            "minMessage" => "La quantitée ne peut être inférieure à 0",
                        )
                    ),
                ),
                "attr" => array(
                    "step" => "1",
                    "min" => 0,
                    "autocomplete" => "off"
                ),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                "data_class" => 'Decitre\Bundle\B2bBundle\Entity\DeliveryB2BLine',
            )
        );
    }

    public function getBlockPrefix()
    {
        return "delivery_line_form";
    }
}
