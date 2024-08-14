<?php

namespace App\Controller\Admin;

use App\Entity\BettingProvider\SimulationStrategy;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SimulationStrategyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SimulationStrategy::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
