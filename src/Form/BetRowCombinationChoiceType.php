<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Spm\BetRowCombination;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BetRowCombinationChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'combination',
                EntityType::class,
                [
                    'class' => BetRowCombination::class,
                    'choice_label' => 'ident',
                    'row_attr' => ['class' => 'form-row'],
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
            'data_class' => BetRowCombinationChoiceData::class
        ]);
    }
}
