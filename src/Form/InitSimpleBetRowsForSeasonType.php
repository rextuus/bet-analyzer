<?php

namespace App\Form;

use App\Entity\SeasonStatistic;
use App\Entity\SpmSeason;
use App\Service\Evaluation\BetAccumulation;
use App\Service\Evaluation\OddAccumulationVariant;
use App\Service\Sportmonks\Content\Season\SpmSeasonRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;

class InitSimpleBetRowsForSeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'season',
                EntityType::class,
                [
                    'class' => SpmSeason::class,
                    'query_builder' => function (SpmSeasonRepository $er): QueryBuilder {
                        return $er->createQueryBuilder('s')
                            ->select('s')
                            ->innerJoin(SeasonStatistic::class, 'st', 'WITH', 'st.seasonApiId = s.apiId')
                            ->where('st.manuallyConfirmed = 1')
                            ;
                    },
                    'choice_label' => 'displayName',
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'min',
                NumberType::class,
                [
                    'scale' => 2,
                    'data' => 1.0,
                    'html5' => true,
                    'attr' => ['step' => 0.1],
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'max',
                NumberType::class,
                [
                    'scale' => 2,
                    'data' => 2.0,
                    'html5' => true,
                    'attr' => ['step' => 0.1],
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'steps',
                NumberType::class,
                [
                    'scale' => 1,
                    'data' => 0.1,
                    'html5' => true,
                    'attr' => ['step' => 0.1],
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'initialCashBox',
                NumberType::class,
                [
                    'scale' => 2,
                    'data' => 100.00,
                    'html5' => true,
                    'attr' => ['step' => 0.5],
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'wager',
                NumberType::class,
                [
                    'scale' => 2,
                    'data' => 1.00,
                    'html5' => true,
                    'attr' => ['step' => 0.5],
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'includeTax',
                CheckboxType::class,
                [
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'oddAccumulationVariant',
                EnumType::class,
                [
                    'class' => OddAccumulationVariant::class,
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'create',
                SubmitType::class,
                [
                    'label' => 'Create Bet-Row',
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InitSimpleBetRowsForSeasonData::class
        ]);
    }
}
