<?php

namespace App\Form;

use App\Entity\Splitter;
use App\Entity\SplitterCategory;
use App\Repository\SplitterCategoryRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SplitterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'row_attr' => ['class' => 'form-floating mb-3 text-dark'],
                'label' => 'Nom du splitter',
                'attr' => ['placeholder' => 'pseudo']
            ])
            ->add('description', null, [
                'row_attr' => ['class' => 'form-floating mb-3  text-dark'],
                'label' => 'Description du splitter',
                'attr' => ['placeholder' => 'pseudo']
            ])
            ->add('category', EntityType::class, [
                'class' => SplitterCategory::class,
                'required' => true,
                'row_attr' => ['class' => 'form-floating mb-3  text-dark'],
                'label' => 'Catégorie',
                'query_builder' => function (SplitterCategoryRepository $repository) {
                    return $repository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
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
