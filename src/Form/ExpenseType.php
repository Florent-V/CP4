<?php

namespace App\Form;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Splitter;
use App\Entity\User;
use App\Repository\CategoryRepository;
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
                'class' => User::class,
                'row_attr' => ['class' => 'form-floating mb-3  text-dark'],
                'label' => 'Payé par :',
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->innerJoin('u.splitters', 's')
                        ->where('s.uniqueId = :uniqueId')
                        ->setParameter('uniqueId', $this->splitter->getUniqueId())
                    ->orderBy('u.firstName', 'ASC');
                },
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
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
