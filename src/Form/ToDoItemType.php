<?php

namespace App\Form;

use App\Entity\ToDoItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToDoItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'row_attr' => ['class' => 'form-floating mb-3 text-dark'],
                'label' => 'Element de la ToDoList',
                'attr' => ['placeholder' => 'e.g Fromage, Pain, Beurre, Jambon...']
            ])
            //->add('toDoList')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ToDoItem::class,
        ]);
    }
}
