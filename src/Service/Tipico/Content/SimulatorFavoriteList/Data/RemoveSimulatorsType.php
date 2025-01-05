<?php

declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList\Data;

use App\Entity\BettingProvider\Simulator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoveSimulatorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('simulators', EntityType::class, [
                'class' => Simulator::class,
                'choices' => $options['simulators'], // Use the passed simulators as choices
                'multiple' => true,
                'expanded' => true, // Use checkboxes for better UX
                'choice_label' => 'identifierWithCashBox', // Adjust based on your Simulator entity's fields
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Remove Selected Simulators',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'simulators' => [], // Pass available simulators as an option
        ]);

        $resolver->setAllowedTypes('simulators', ['array', 'Traversable']); // Ensure valid type
    }
}
