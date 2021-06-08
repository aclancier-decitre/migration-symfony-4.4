<?php

namespace App\Form\B2bForm;

use App\Entity\B2bEntity\Periode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PeriodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'attr' => [
                    'class' => 'periode-id'
                ]
            ])
            ->add('libelle', HiddenType::class, [
                'attr' => [
                    'class' => 'periode-libelle'
                ],
                'mapped' => false,
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control date-debut'
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control date-fin'
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->addEventListener(FormEvents::SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * Vérification des dates lors de l'envoi du formulaire
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $dateDebut = $data->getDateDebut();
        $dateFin = $data->getDateFin();

        if ($dateDebut === null || $dateFin === null) {
            $event->getForm()->addError(new FormError('Les dates de début et de fin sont obligatoires.'));
            return;
        }

        if ($dateDebut === false || $dateFin === false) {
            $event->getForm()->addError(new FormError('Erreur lors de la conversion des dates.'));
            return;
        }

        if ($dateFin < $dateDebut) {
            $event->getForm()->addError(
                new FormError('La date de fin ne peut être antérieure à la date de début.')
            );
            return;
        }

        if ($dateFin->format('Y-m-d') === $dateDebut->format('Y-m-d')) {
            $event->getForm()->addError(new FormError('La date de fin ne peut être égale à la date de début.'));
            return;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => Periode::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'decitre_b2b_bundle_periode_type';
    }
}
