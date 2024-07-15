<?php

namespace App\Controller\Admin;

use App\Entity\Cancellation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CancellationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cancellation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Poltiques d\'annulation :')
            ->setPageTitle('new', 'Créer une politique')
            ->setPageTitle('edit', fn (Cancellation $concellation) => (string) $concellation->getName())
            ->setEntityLabelInSingular('une politique d\'annulation')
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Nom de la politique')
            ->setFormTypeOptions(['attr' => ['placeholder' => 'Ex: Flexible']]),
            TextField::new('slug', 'Slug')->onlyOnIndex(),
            TextareaField::new('description', 'Description'),
        ];
    }
}
