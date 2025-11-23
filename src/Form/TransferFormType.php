<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\Splitter;
use App\Entity\Transfer;
use App\Repository\MemberRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferFormType extends AbstractType
{
    private Splitter $splitter;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->splitter = $options['splitter'];

        $builder
            ->add('amount', MoneyType::class, [
                'currency' => false,
                'label' => 'Montant',
                'attr' => [
                    'placeholder' => '0.00',
                ],
            ])
            ->add('madeAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'label' => 'Date du transfert',
            ])
            ->add('fromMember', EntityType::class, [
                'class' => Member::class,
                'label' => 'Membre qui donne l\'argent',
                'query_builder' => function (MemberRepository $memberRepository) {
                    return $memberRepository->createQueryBuilder('m')
                        ->innerJoin('m.splitter', 's')
                        ->where('s.uniqueId = :uniqueId')
                        ->setParameter('uniqueId', $this->splitter->getUniqueId())
                        ->orderBy('m.nickname', 'ASC');
                },
                'choice_label' => function (Member $member) {
                    return $member->getNickname();
                },
                'placeholder' => 'Sélectionner un membre',
            ])
            ->add('toMember', EntityType::class, [
                'class' => Member::class,
                'label' => 'Membre qui reçoit l\'argent',
                'query_builder' => function (MemberRepository $memberRepository) {
                    return $memberRepository->createQueryBuilder('m')
                        ->innerJoin('m.splitter', 's')
                        ->where('s.uniqueId = :uniqueId')
                        ->setParameter('uniqueId', $this->splitter->getUniqueId())
                        ->orderBy('m.nickname', 'ASC');
                },
                'choice_label' => function (Member $member) {
                    return $member->getNickname();
                },
                'placeholder' => 'Sélectionner un membre',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Remboursement restaurant',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transfer::class,
            'splitter' => null,
        ]);
    }
}
