<?php

namespace App\Form\B2bForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChorusDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod("POST");
        $builder->add(
            "typeDoc",
            HiddenType::class,
            array(
                "label" => false,
                "required" => false,
            )
        )->add(
            "chorusDocumentId",
            HiddenType::class,
            array(
                "label" => false,
                "required" => false,
            )
        )->add(
            "siretId",
            TextType::class,
            array(
                "required" => true,
                "label" => "Numéro de SIRET",
                "constraints" => [
                    new NotBlank(["message" => "Veuillez renseigner un numéro de SIRET"]),
                    new Length([
                        "min" => 14,
                        "max" => 14,
                        "exactMessage" => "Le numéro de SIRET doit comporter 14 caractères"
                    ]),
                ],
            )
        )->add(
            "engagementId",
            TextType::class,
            array(
                "required" => false,
                "label" => "Numéro d'engagement",
            )
        )->add(
            "codeService",
            TextType::class,
            array(
                "required" => false,
                "label" => "Code service",
            )
        )->add(
            "save",
            SubmitType::class,
            array(
                "label" => "Sauvegarder",
                "attr" => [
                    "class" => "dct-button dct-button-validate"
                ]
            )
        );
    }
}
