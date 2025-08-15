<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberSelectionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('member', ChoiceType::class, [
                'choices' => $options['members'],
                'choice_label' => function (?Member $member) {
                    return $member ? $member->getNickname() : '';
                },
                'choice_value' => 'id',
                'expanded' => true, // radio
                'multiple' => false,
                'label' => 'Qui êtes-vous dans ce groupe ?',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'members' => [],
        ]);
    }
}
