<?php

namespace App\Service\Tipico\Content\Simulator\Data;

use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimulatorFilterType extends AbstractType
{
    public function __construct(private SimulationStrategyProcessorProvider $processorProvider)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $idents = $this->processorProvider->getIdents();
        $choices = [];
        foreach ($idents as $ident) {
            $choices[$ident] = $ident;
        }

        $builder
            ->add('excludeNegative', CheckboxType::class, ['attr' => ['checked' => 1], 'required' => false])
            ->add('variant', ChoiceType::class, ['multiple' => true, 'choices' => $choices, 'required' => false])
            ->add(
                'minCashBox',
                NumberType::class,
                [
                    'scale' => 2,
//                    'data' => 100.0,
                    'html5' => true,
                    'attr' => ['step' => 0.1],
                    'row_attr' => ['class' => 'form-row'],
                    'required' => false
                ]
            )
            ->add(
                'maxCashBox',
                NumberType::class,
                [
                    'scale' => 2,
//                    'data' => 1000.0,
                    'html5' => true,
                    'attr' => ['step' => 0.1],
                    'row_attr' => ['class' => 'form-row'],
                    'required' => false
                ]
            )
            ->add(
                'minBets',
                NumberType::class,
                [
//                    'data' => 1,
                    'html5' => true,
                    'row_attr' => ['class' => 'form-row'],
                    'required' => false
                ]
            )
            ->add(
                'maxBets',
                NumberType::class,
                [
//                    'data' => 100000,
                    'html5' => true,
                    'row_attr' => ['class' => 'form-row'],
                    'required' => false
                ]
            )
            ->add('filter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SimulatorFilterData::class
        ]);
    }
}
