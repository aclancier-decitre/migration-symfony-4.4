<?php

namespace App\Form\CoreForm;

use Symfony\Component\Form\AbstractType;

class SubSubFamilyType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'sub_sub_family';
    }

    public function getParent()
    {
        return FamilyWithSubType::class;
    }
}
