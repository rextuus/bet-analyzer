<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TipicoSimulatorFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'combination',
                ChoiceType::class,
                [
                    'choice_label' => 'ident',
                    'row_attr' => ['class' => 'form-row'],
                    'choices'  => [
                        'Maybe' => null,
                        'Yes' => true,
                        'No' => false,
                    ],
                ]
            )
            ->add(
                'calculate',
                SubmitType::class,
                [
                    'label' => 'Choice',
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
