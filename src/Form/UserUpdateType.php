<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', null, [
                'row_attr' => ['class' => 'form-floating mb-3'],
                'label' => 'Pseudo',
                'attr' => ['placeholder' => 'pseudo']
            ])
            ->add('firstName', null, [
                'row_attr' => ['class' => 'form-floating mb-3'],
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Pierre...']
            ])
            ->add('lastName', null, [
                'row_attr' => ['class' => 'form-floating mb-3'],
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Dupond...']
            ])
            ->add('email', EmailType::class, [
                'row_attr' => ['class' => 'form-floating mb-3'],
                'label' => 'Email',
                'attr' => ['placeholder' => 'name@example.fr']
            ])
            ->add('phone', null, [
                'row_attr' => ['class' => 'form-floating mb-3'],
                'label' => 'Téléphone',
                'attr' => ['placeholder' => '+33...']
            ])
            ->add('pictureFile', VichFileType::class, [
                'label' => 'Photo',
                'attr' => ['placeholder' => 'photo'],
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
