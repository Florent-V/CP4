<?php

namespace App\Form;

use App\Entity\ToDoList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToDoListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'row_attr' => ['class' => 'form-floating mb-3 text-dark'],
                'label' => 'Titre de la ToDoList',
                'attr' => ['placeholder' => 'e.g. Liste de courses']
            ])
            ->add('description', null, [
                'row_attr' => ['class' => 'form-floating mb-3 text-dark'],
                'label' => 'Description de la ToDoList',
                'attr' => ['placeholder' => 'e.g Liste de courses pour le repas de ce soir']
            ])
            ->add('toDoItems', CollectionType::class, [
                'entry_type' => ToDoItemType::class,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'row_attr' => ['class' => 'form-floating mb-3 text-dark'],
                'label' => 'Elements de la ToDoList',
                'attr' => ['placeholder' => 'e.g Fromage, Pain, Beurre, Jambon...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ToDoList::class,
        ]);
    }
}
