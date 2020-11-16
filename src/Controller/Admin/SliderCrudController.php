<?php

namespace App\Controller\Admin;

use App\Entity\Slider;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SliderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Slider::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre du header'),
            TextEditorField::new('content', 'contenu'),
            TextField::new('btnTitle', 'Titre du Bouton'),
            TextField::new('btnUrl', 'Url du Bouton'),
            ImageField::new('picture')->setBasePath('uploads/')->setFormTypeOptions(['mapped' => false, 'required' => false]),
        ];
    }
   
}
