<?php

namespace App\Form;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Member;
use App\Entity\Splitter;
use App\Enum\SplitType;
use App\Repository\CategoryRepository;
use App\Repository\MemberRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ExpenseFormType extends AbstractType
{
    private Splitter $splitter;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->splitter = $options['splitter'];

        $builder
            ->add('name')
            ->add('pictureFile', VichImageType::class, [
                'label' => 'Photo',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                'attr' => [
                    'accept' => 'image/jpeg, image/png, image/gif',
                ],
            ])
            ->add('madeAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('amount', MoneyType::class, [
                'currency' => false,
                'attr' => [
                    'min' => '0.01',
                    'step' => '0.01',
                ],
            ])
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
            ->add('splitType', EnumType::class, [
                'class' => SplitType::class,
                'label' => 'Répartition',
                'choice_label' => fn (SplitType $type) => $type->label(),
            ])
            ->add('shares', CollectionType::class, [
                'entry_type' => ExpenseShareFormType::class,
                'entry_options' => [
                    'splitter' => $this->splitter,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);

        // PRE_SUBMIT: if splitType is equal, discard any submitted shares
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            if (($data['splitType'] ?? SplitType::EQUAL->value) === SplitType::EQUAL->value) {
                $data['shares'] = [];
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
            'splitter' => null,
        ]);
    }
}
