<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
abstract class LiveComponentFormController extends AbstractController
{
    private mixed $formTarget;

    protected function instantiateForm(): FormInterface
    {
        $this->initEditFormTarget();

        if (!$this->formTarget) {
            throw new Exception('Form target needs to be set for access grant!');
        }

        if (!$this->validateFormTarget()) {
            throw new Exception('Invalid form target given');
        }

        return $this->instantiateFormForLiveComponent();
    }


    abstract public function initEditFormTarget(): void;

    abstract public function instantiateFormForLiveComponent(): FormInterface;

    abstract public function validateFormTarget(): bool;

    protected function getFormTarget(): mixed
    {
        return $this->formTarget;
    }

    protected function setFormTarget(mixed $formTarget): LiveComponentFormController
    {
        $this->formTarget = $formTarget;

        return $this;
    }
}
