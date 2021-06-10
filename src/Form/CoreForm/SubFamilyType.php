<?php

namespace App\Form\CoreForm;

use Symfony\Component\Form\AbstractType;

class SubFamilyType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'sub_family';
    }

    public function getParent()
    {
        return FamilyWithSubType::class;
    }
}
