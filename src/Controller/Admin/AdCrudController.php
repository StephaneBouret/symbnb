<?php

namespace App\Controller\Admin;

use App\Entity\Ad;
use App\Entity\User;
use App\Form\AdImageFormType;
use Doctrine\ORM\QueryBuilder;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDto;

class AdCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Ad::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Annonces :')
            // ->setPageTitle('new', 'Créer une annonce')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('une Annonce')
            ->setPageTitle('edit', fn (Ad $ad) => (string) 'Modifier ' . $ad->getName())
            ->setPageTitle('detail', fn (Ad $ad) => (string) 'Modifier ' . $ad->getName());
    }

    // public function configureActions(Actions $actions): Actions
    // {
    //     return $actions
    //         ->setPermission(Action::NEW, 'ROLE_USER')
    //         ->setPermission(Action::EDIT, 'ADMIN_USER_EDIT');
    // }

    // public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    // {
    //     $user = $this->security->getUser();
    //     $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
    //     if ($this->isGranted('ROLE_ADMIN')) {
    //         return $queryBuilder;
    //     }
    //     $queryBuilder
    //         ->andWhere('entity.author = :user')
    //         ->setParameter('user', $user);
    //     return $queryBuilder;
    // }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            FormField::addColumn(6),
            TextField::new('name', 'Nom de l\'annonce'),
            MoneyField::new('price', 'Prix de la nuitée')
                ->setCurrency('EUR')
                ->setTextAlign('left')
                ->setFormTypeOption('divisor', 100),
            NumberField::new('capacity', 'Capacité du logement')->setNumDecimals(0),
            NumberField::new('rooms', 'Nombre de chambre')->setNumDecimals(0),
            NumberField::new('beds', 'Nombre de lits')->setNumDecimals(0),
            DateField::new('createdAt', 'Date de création')
                ->setFormat('long')
                ->renderAsChoice()
                ->onlyOnIndex(),
            CollectionField::new('images', 'Images')
                ->setEntryType(AdImageFormType::class)
                ->setFormTypeOption('by_reference', false)
                ->hideOnIndex(),
            AssociationField::new('equipment', 'Equipements')
                ->setFormTypeOptions([
                    'query_builder' => function (EntityRepository $er): QueryBuilder {
                        return $er->createQueryBuilder('e')
                            ->orderBy('e.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'group_by' => function ($choice, $key, $value) {
                        return $choice->getCriteria()->getName();
                    },
                    'multiple' => true,
                    'expanded' => false,
                    // 'expanded' => true,
                    'by_reference' => false,
                ]),
            FormField::addColumn(6),
            AssociationField::new('author', 'Auteur')
                ->setRequired(true)
                ->setFormTypeOptions([
                    'class' => User::class, // Utilisez l'entité User
                    'choice_label' => 'fullname', // Ou toute autre méthode que vous avez définie pour afficher l'utilisateur dans la liste déroulante
                ]),
            TextField::new('city', 'Ville de l\'annonce')->hideOnIndex(),
            CountryField::new('country', 'Pays de l\'annonce')->hideOnIndex(),
            TextEditorField::new('content', 'Contenu de l\'annonce')->hideOnIndex(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setCity(ucfirst($entityInstance->getCity()));
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setCity(ucfirst($entityInstance->getCity()));
        parent::updateEntity($entityManager, $entityInstance);
    }
}
