<?php

namespace App\Form\CoreForm;

use App\Entity\ThesaurusEntity\Family;
use App\Entity\ThesaurusEntity\SubFamily;
use App\Entity\ThesaurusEntity\SubSubFamily;
use App\Repository\ThesaurusRepository\FamilyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FamilyWithSubType extends AbstractType
{
    /**
     * @var FamilyRepository
     */
    private FamilyRepository $familyRepository;

    /**
     * @var Family[]
     */
    private array $families;

    private const FAMILY_TYPES = [
        'family' => 'Famille',
        'sub_family' => 'Sous-Famille',
        'sub_sub_family' => 'Sous-Sous-Famille'
    ];

    public function __construct(FamilyRepository $familyRepository)
    {
        $this->familyRepository = $familyRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $class = '';
        switch ($options['family_type']) {
            case 'family':
                $this->families = $this->familyRepository->findAll();
                $class = 'family-select';
                $dataClass = Family::class;
                break;
            case 'sub_family':
                $this->families = $options['parent_family_id'] ?
                    $this->familyRepository->findSubFamiliesByFamilyId($options['parent_family_id']) : [];
                $class = 'sub-family-select';
                $dataClass = SubFamily::class;
                break;
            case 'sub_sub_family':
                $this->families = $options['parent_family_id'] ?
                    $this->familyRepository->findSubSubFamiliesBySubFamilyId($options['parent_family_id']) : [];
                $class = 'sub-sub-family-select';
                $dataClass = SubSubFamily::class;
                break;
        }

        $builder->add('family', ChoiceType::class, [
            'label' => self::FAMILY_TYPES[$options['family_type']],
            'choices' => $this->families,
            'choice_value' => 'id',
            'choice_label' => function ($family) {
                return $family->getId() . ' - ' . $family->getLabel();
            },
            'multiple' => $options['multiple'],
            'expanded' => $options['expanded'],
            'required' => $options['required'],
            'attr' => [
                'class' => $class,
            ],
            'data_class' => $dataClass,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'family_type' => 'family',
            'parent_family_id' => null,
            'multiple' => true,
            'expanded' => false,
            'required' => true,
        ]);

        $resolver->setAllowedValues('family_type', array_keys(self::FAMILY_TYPES));
    }

    public function getBlockPrefix()
    {
        return 'family';
    }
}
