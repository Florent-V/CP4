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
            ->add('name', null, [
                'row_attr' => ['class' => 'form-floating mb-3 text-dark'],
                'label' => 'Nom',
                'attr' => ['placeholder' => 'pseudo']
            ])
            ->add('madeAt', DateType::class, [
                'row_attr' => ['class' => 'form-floating mb-3 text-dark'],
                'label' => 'Date',
                'attr' => ['placeholder' => 'date..'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('amount', MoneyType::class, [
                'row_attr' => ['class' => 'mb-3 text-light'],
                'label' => 'Montant',
                'attr' => ['placeholder' => 'Montant']
            ])
            ->add('paidBy', EntityType::class, [
                'class' => Member::class,
                'row_attr' => ['class' => 'form-floating mb-3  text-dark'],
                'label' => 'Payé par :',
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
                'required' => true,
                'row_attr' => ['class' => 'form-floating mb-3  text-dark'],
                'label' => 'Catégorie',
                'query_builder' => function (CategoryRepository $repository) {
                    return $repository->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('beneficiaries', EntityType::class, [
                'class' => Member::class,
                'row_attr' => ['class' => 'mb-3  text-white'],
                'label' => 'Bénéficiaires :',
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
