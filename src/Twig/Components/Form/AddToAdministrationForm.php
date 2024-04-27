<?php
declare(strict_types=1);

namespace App\Twig\Components\Form;

use App\Controller\LiveComponentFormController;
use App\Entity\BettingProvider\Simulator;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(route: 'live_component_app')]
class AddToAdministrationForm extends LiveComponentFormController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?AddSimulatorToListData $initialFormData = null;

    #[LiveProp]
    public Simulator $simulator;

    #[LiveProp]
    public string $formAction;

    public function initEditFormTarget(): void
    {
        $this->setFormTarget($this->simulator);
    }

    public function instantiateFormForLiveComponent(): FormInterface
    {
        /** @var Simulator $simulator */
        $simulator = $this->getFormTarget();

        return $this->createForm(
            AddSimulatorToListType::class,
            $this->initialFormData,
            [
                'simulator' => $simulator,
            ]
        );
    }

    public function validateFormTarget(): bool
    {
        return $this->getFormTarget() instanceof Simulator;
    }
}
