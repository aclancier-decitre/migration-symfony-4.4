<?php

namespace App\Form\CoreForm;

use App\Entity\AppEntity\Periode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class DateIntervalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_debut', DateType::class, [
                'label' => $options['date_debut_label'],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => $options['min_date_debut'],
                    'disabled' => $options['default-disabled'],
                ],
                'required' => $options['date_debut_required'],
                'constraints' => [
                    new Date(),
                    new GreaterThan([
                        'value' => $options['min_date_debut'],
                        'message' => sprintf(
                            "La date doit être ultérieure au %s",
                            (new \DateTime($options['min_date_debut']))->format('d/m/Y')
                        ),
                    ]),
                ],
            ])
            ->add('date_fin', DateType::class, [
                'label' => $options['date_fin_label'],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'max' => $options['max_date_fin'],
                    'disabled' => $options['default-disabled'],
                ],
                'required' => $options['date_fin_required'],
                'constraints' => [
                    new Date(),
                    new LessThanOrEqual([
                        'value' => $options['max_date_fin'],
                        'message' => sprintf(
                            "La date doit être antérieure ou égale au %s",
                            (new \DateTime($options['max_date_fin']))->format('d/m/Y')
                        ),
                    ]),
                ],
            ]);
    }

    public function validate($data, ExecutionContextInterface $context)
    {
        if ($data instanceof Periode) {
            $startDate = $data->getDateDebut();
            $endDate = $data->getDateFin();
        } else {
            $startDate = $data['date_debut'];
            $endDate = $data['date_fin'];
        }

        $options = $context->getObject()->getConfig()->getOptions();

        if ($startDate !== null && $endDate !== null) {
            if ($startDate > $endDate) {
                $context->buildViolation('La date de début doit être inférieure ou égale à la date de fin.')
                    ->atPath('date_debut')
                    ->addViolation();
                return;
            }
        }

        if ($options['is_both_required']) {
            if ($startDate !== null && $endDate === null) {
                $context->buildViolation('La date de fin est obligatoire car une date de début a été saisie.')
                    ->atPath('date_fin')
                    ->addViolation();
                return;
            }

            if ($endDate !== null && $startDate === null) {
                $context->buildViolation('La date de début est obligatoire car une date de fin a été saisie.')
                    ->atPath('date_debut')
                    ->addViolation();
                return;
            }
        }

        if ($options['max_days_interval']) {
            $maxDaysInterval = $options['max_days_interval'];

            if ($startDate !== null || $endDate !== null) {
                $daysDifference = $startDate->diff($endDate)->days;

                if ($daysDifference > $maxDaysInterval) {
                    $context->buildViolation("L'intervalle de date doit être inférieur à {{ max }} jours.")
                        ->setParameter('{{ max }}', $maxDaysInterval)
                        ->atPath('date_debut')
                        ->addViolation();
                    return;
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'date_debut_label' => 'Date de début',
            'date_fin_label' => 'Date de fin',
            'min_date_debut' => '1900-01-01',
            'max_date_fin' => (new \DateTime())->format('Y-m-d'),
            'max_days_interval' => null,
            'is_both_required' => false,
            'date_debut_required' => true,
            'date_fin_required' => true,
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'validate'],
                ]),
            ],
            'data_class' => null,
            'default-disabled' => false,
        ]);

        $resolver->setAllowedTypes('min_date_debut', 'string');
        $resolver->setAllowedTypes('max_date_fin', 'string');
    }

    public function getBlockPrefix()
    {
        return 'decitre_date_interval_type';
    }
}
