<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\SimulatorFavoriteList\Data;

use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AddSimulatorToListType extends AbstractType
{


    public function __construct(private SimulatorFavoriteListService $favoriteListService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $lists = $this->favoriteListService->findListsNotContainingSimulator($options['simulator']);

        $builder
            ->add('simulatorFavoriteList', ChoiceType::class, [
                'choices' => $lists,
                'choice_label' => function (SimulatorFavoriteList $favoriteList) {
                    return $favoriteList->getIdentifier(); // Assuming 'id' is the identifier property
                },
                'label' => 'Select a Simulator Favorite List',
            ])
            ->add('add', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddSimulatorToListData::class,
            'simulator' => null,
        ]);
    }
}
