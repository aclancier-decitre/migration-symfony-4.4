<?php

namespace App\Form\ProductForm;

use App\Form\CoreForm\DateIntervalType;
use App\Form\CoreForm\FamiliesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
        $builder
            ->add('ean', TextType::class, [
                'label' => 'EAN',
                'required' => false,
                'attr' => [
                    'class' => 'product-ean',
                ]
            ])
            ->add('product_title', TextType::class, [
                'label' => 'Titre du produit',
                'required' => false,
                'attr' => [
                    'class' => 'product-title'
                ],
            ])
            ->add('author_name', TextType::class, [
                'label' => "Nom de l'auteur",
                'required' => false,
                'attr' => [
                    'class' => 'author-name'
                ]
            ])
            ->add('publisher_id', HiddenType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'publisher-id'
                ]
            ])
            ->add('publisher_name', TextType::class, [
                'label' => 'Editeur',
                'required' => false,
                'attr' => [
                    'class' => 'publisher-name'
                ]
            ])
            ->add('collection_id', HiddenType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'collection-id'
                ]
            ])
            ->add('collection_name', TextType::class, [
                'label' => 'Collection',
                'required' => false,
                'attr' => [
                    'class' => 'collection-name'
                ]
            ])
            ->add(
                'published_at',
                DateIntervalType::class,
                [
                    'label' => 'Date de parution',
                    'date_debut_required' => false,
                    'date_fin_required' => false,
                    'date_debut_label' => 'Paru entre le',
                    'date_fin_label' => 'et le',
                ]
            )
            ->add('family', FamiliesType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('csrf_protection', false);
    }

    public function getBlockPrefix()
    {
        return 'product_search';
    }
}
