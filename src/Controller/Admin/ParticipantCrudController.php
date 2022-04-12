<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextEditorField::new('pseudo'),
            TextEditorField::new('prenom'),
            TextEditorField::new('nom'),
            TextEditorField::new('telephone'),
            TextEditorField::new('mail'),
            TextEditorField::new('motPasse'),
            BooleanField::new('administrateur'),
            BooleanField::new('actif'),
            ArrayField::new('roles'),
            IdField::new('campusId'),
        ];
    }
}
