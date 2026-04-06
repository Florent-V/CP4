<?php

namespace App\Form;

use App\Entity\ExpenseShare;
use App\Entity\Member;
use App\Entity\Splitter;
use App\Repository\MemberRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseShareFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Splitter|null $splitter */
        $splitter = $options['splitter'];

        $memberOptions = [
            'class' => Member::class,
            'choice_label' => 'nickname',
            'label' => false,
            'attr' => [
                'class' => 'form-select form-select-sm',
            ],
        ];

        if ($splitter !== null) {
            $memberOptions['query_builder'] = function (MemberRepository $memberRepository) use ($splitter) {
                return $memberRepository->createQueryBuilder('m')
                    ->innerJoin('m.splitter', 's')
                    ->where('s.uniqueId = :uniqueId')
                    ->setParameter('uniqueId', $splitter->getUniqueId())
                    ->orderBy('m.nickname', 'ASC');
            };
        }

        $builder
            ->add('member', EntityType::class, $memberOptions)
            ->add('share', NumberType::class, [
                'label' => false,
                'scale' => 2,
                'attr' => [
                    'step' => '0.01',
                    'min' => '0',
                    'class' => 'form-control form-control-sm',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseShare::class,
            'splitter' => null,
        ]);
        $resolver->setAllowedTypes('splitter', ['null', Splitter::class]);
    }
}
