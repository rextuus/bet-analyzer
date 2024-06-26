<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Spm\SpmLeague;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SeasonStatisticFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'league',
                EntityType::class,
                [
                    'class' => SpmLeague::class,
//                    'query_builder' => function (SpmSeasonRepository $er): QueryBuilder {
//                        return $er->createQueryBuilder('s')
//                            ->select('s')
//                            ->innerJoin(SeasonStatistic::class, 'st', 'WITH', 'st.seasonApiId = s.apiId')
//                            ->where('st.manuallyConfirmed = 1')
//                            ;
//                    },
                    'choice_label' => 'name',
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'min',
                NumberType::class,
                [
                    'scale' => 2,
                    'data' => 100.0,
                    'html5' => true,
                    'attr' => ['step' => 0.1],
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'calculate',
                SubmitType::class,
                [
                    'label' => 'Calculate',
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LeagueStatisticFilterData::class
        ]);
    }
}
