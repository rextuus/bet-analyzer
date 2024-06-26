<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BetRowCombinationCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'ident',
                TextType::class,
                [
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'active',
                CheckboxType::class,
                [
                    'required' => false,
                    'data' => true,
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
            ->add(
                'calculate',
                SubmitType::class,
                [
                    'label' => 'Create',
                    'row_attr' => ['class' => 'form-row'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BetRowCombinationCreateData::class
        ]);
    }
}
