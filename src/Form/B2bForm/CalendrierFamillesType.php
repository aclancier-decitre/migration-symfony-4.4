<?php

namespace App\Form\B2bForm;

use App\Entity\B2bEntity\CalendrierB2B;
use App\Entity\B2bEntity\Famille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CalendrierFamillesType extends AbstractType
{

    private $calendriers;
    private $mode;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->calendriers = $options['calendriers'];
        $this->mode = $options['mode'];

        $builder
            ->add('mode', HiddenType::class, [
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'mode-input',
                    'readonly' => true,
                ],
            ])
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'attr' => [
                    'class' => 'libelle-input',
                    'readonly' => true,
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new NotNull()
                ]
            ])
            ->add('famillesAssignees', ChoiceType::class, [
                'choices' => $options['familles'],
                'choice_label' => function (Famille $famille) {
                    return (string) $famille;
                },
                'choice_value' => 'code',
                'multiple' => true,
                'label' => 'Familles assignées',
                'attr' => [
                    'class' => 'familles-select',
                ],
                'constraints' => [
                    new NotBlank(),
                    new NotNull()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['familles', 'calendriers', 'mode']);

        $resolver->setDefaults(array(
            'data_class' => CalendrierB2B::class,
            'csrf_protection' => false,
            'mapped' => true,
            'constraints' => new Callback([
                'callback' => function ($data, ExecutionContextInterface $context) {
                    if ($this->mode === null) {
                        $context->addViolation('Mode requis.');
                    }

                    // Vérifie que le libellé ne contient pas de /, car il est utilisé dans l'URL
                    if (strpos($data->getLibelle(), '/') != false) {
                        $context->addViolation(
                            "Le libellé ne peut pas contenir le caractère /"
                        );
                    }

                    foreach ($this->calendriers as $calendrierUnitaire) {
                        if ($this->mode != 'create') {
                            if ($calendrierUnitaire->getLibelle() === $data->getLibelle()) {
                                continue;
                            }
                        } else {
                            if ($calendrierUnitaire->getLibelle() === $data->getLibelle()) {
                                $context->addViolation('Libellé déjà utilisé pour ce client.');
                            }
                        }

                        foreach ($data->getFamillesAssignees() as $codeFamille) {
                            if ($calendrierUnitaire->hasCodeFamilleInFamillesAssignees($codeFamille)) {
                                $context->addViolation(
                                    'La famille ' . $codeFamille . ' est déjà utilisée pour ce client.'
                                );
                            }
                        }
                    }
                }
            ]),
        ));
    }

    public function getBlockPrefix()
    {
        return 'decitre_b2b_bundle_calendrier_familles_type';
    }
}
