<?php

namespace App\Controller\Admin;

use App\Entity\Type;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Type::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Types de logement :')
            ->setPageTitle('new', 'Créer un type')
            ->setPageTitle('edit', fn (Type $type) => (string) $type->getName())
            ->setEntityLabelInSingular('un Type')
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->onlyOnIndex(),
            TextField::new('name', 'Type de logement')
                ->setFormTypeOptions(['attr' => ['placeholder' => 'Ex: Appartement']]),
            TextField::new('slug', 'Slug')->onlyOnIndex(),
            ImageField::new('imageName', 'Image :')
                ->setBasePath('/images/types')
                ->onlyOnIndex(),
            TextField::new('imageFile', 'Fichier SVG :')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions(['delete_label' => 'Supprimer le SVG'])
                ->hideOnIndex(),
        ];
    }
}
