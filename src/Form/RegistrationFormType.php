<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
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
            ->add('phone', null, [
                'row_attr' => ['class' => 'form-floating mb-3'],
                'label' => 'Téléphone',
                'attr' => ['placeholder' => '+33...']
            ])
            ->add('email', null, [
                'row_attr' => ['class' => 'form-floating mb-3'],
                'label' => 'Email',
                'attr' => ['placeholder' => 'name@example.fr']
            ])
/*            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])*/
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '*****'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
