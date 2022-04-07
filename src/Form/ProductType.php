<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>false
            ])
            ->add('price', NumberType::class,[
                'label'=>false
            ])
            ->add('dlimage', FileType::class,[
                'mapped'=>false,
                'required'=>false,
                'label'=> false,
                'constraints'=> [
                    new File([
                        'mimeTypes'=> [
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Veuillez upload un image au bon format',
                    ])
                ]
            ])
            ->add('excerpt', TextType::class,[
                'label'=>false
            ])
            ->add('description', TextareaType::class,[
                'label'=>false
            ])
            ->add('online',CheckboxType::class,[
                'label'=>false,
                'required'=>false
            ])
            ->add('category',EntityType::class,[
                "label"=> false,
                'class'=>Category::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Selectionnez une catégorie'

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
