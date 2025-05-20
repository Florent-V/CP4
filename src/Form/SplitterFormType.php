<?php

namespace App\Form;

use App\Entity\Splitter;
use App\Entity\SplitterCategory;
use App\Repository\SplitterCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class SplitterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du groupe',
                'attr' => ['placeholder' => 'Voyage à ...']
            ])
            ->add('description', null, [
                'label' => 'Description du groupe',
                'attr' => ['placeholder' => 'Groupe pour le voyage à ...']
            ])
            ->add('category', EntityType::class, [
                'class' => SplitterCategory::class,
                'required' => true,
                'label' => 'Catégorie',
                'query_builder' => function (SplitterCategoryRepository $repository) {
                    return $repository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('members', LiveCollectionType::class, [
                'entry_type' => MemberFormType::class,
                'entry_options' => [
                    'label' => false,
                    'constraints' => new Valid()
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                //'prototype' => true,
                'by_reference' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Splitter::class,
        ]);
    }
}
