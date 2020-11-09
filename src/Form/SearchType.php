<?php

namespace App\Form;

use App\Data\Search;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        ->add('string', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'placeholder' => "search..."
            ]
        ])
        ->add('categories', EntityType::class, [
            'required' => false,
            'label' => false,
            'class' => Category::class,
            'multiple' => true,
            'expanded' => true
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Filter',
            'attr' => [
                'class' => 'btn btn-info px-4'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false    
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}