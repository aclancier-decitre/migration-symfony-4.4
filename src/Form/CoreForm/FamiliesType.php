<?php

namespace App\Form\CoreForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class FamiliesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    private function addElements(FormInterface $form, int $familyId = null, int $subFamilyId = null)
    {
        $form->add('family', FamilyWithSubType::class, [
            'label' => false,
            'multiple' => false,
            'required' => false,
        ]);

        $form->add('sub_family', SubFamilyType::class, [
            'label' => false,
            'multiple' => false,
            'required' => false,
            'family_type' => 'sub_family',
            'parent_family_id' => $familyId,
        ]);

        $form->add('sub_sub_family', SubSubFamilyType::class, [
            'label' => false,
            'multiple' => false,
            'required' => false,
            'family_type' => 'sub_sub_family',
            'parent_family_id' => $subFamilyId,
        ]);
    }

    /**
     * Met en place les inputs dans le formulaire
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $this->addElements($form);
    }

    /**
     * Ajoute les inputs en fonction des données envoyées (id de famille et sous-famille)
     */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $selectedFamilyId = (int) $data['family']['family'];
        $selectedSubFamilyId = (int) $data['sub_family']['family'];

        $this->addElements($form, $selectedFamilyId, $selectedSubFamilyId);
    }

    public function getBlockPrefix()
    {
        return 'family_with_sub';
    }
}
