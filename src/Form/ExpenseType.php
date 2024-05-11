<?php

namespace App\Form;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Member;
use App\Entity\Splitter;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseType extends AbstractType
{
    private Splitter $splitter;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->splitter = $options['splitter'];

        $builder
            ->add('name')
            ->add('madeAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('amount', MoneyType::class)
            ->add('paidBy', EntityType::class, [
                'class' => Member::class,
                'query_builder' => function (MemberRepository $memberRepository) {
                    return $memberRepository->createQueryBuilder('m')
                        ->innerJoin('m.splitter', 's')
                        ->where('s.uniqueId = :uniqueId')
                        ->setParameter('uniqueId', $this->splitter->getUniqueId())
                    ->orderBy('m.nickname', 'ASC');
                },
                'choice_label' => function (Member $member) {
                    return $member->getNickname();
                }
            ])
            ->add('category', EntityType::class, [
                'class' => ExpenseCategory::class,
                'label' => 'Catégorie',
                'query_builder' => function (CategoryRepository $repository) {
                    return $repository->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('beneficiaries', EntityType::class, [
                'class' => Member::class,
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
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
            'splitter' => null,
        ]);
    }
}
