<?php

namespace App\Service\Tipico\Duplication;

use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimulatorDuplicationType extends AbstractType
{
    public function __construct(private SimulationStrategyProcessorProvider $processorProvider)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $idents = $this->processorProvider->getIdents();
        $choices = [];
        foreach (Weekday::cases() as $case) {
            $choices[$case->name] = $case->value;
        }

        $builder
            ->add('initProcessing', CheckboxType::class, ['attr' => ['checked' => 1], 'required' => false])
            ->add('weekdays', ChoiceType::class, ['multiple' => true, 'choices' => $choices, 'required' => false])
            ->add('clone', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SimulatorDuplicationData::class
        ]);
    }
}
