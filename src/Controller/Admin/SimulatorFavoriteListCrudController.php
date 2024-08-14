<?php

namespace App\Controller\Admin;

use App\Entity\BettingProvider\SimulatorFavoriteList;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SimulatorFavoriteListCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SimulatorFavoriteList::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('identifier'),
            NumberField::new('totalCashBox'),
            NumberField::new('bets'),
            AssociationField::new('simulators'),
        ];
    }
}
