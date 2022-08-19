<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Name Product'
                ]
            )
            ->add(
                'price',
                NumberType::class,
                [
                    'required' => true,
                    'label' => 'Price',
                    'attr' => [
                        'min' => 10,
                        'max' => 500
                    ]
                ]
            )
            ->add(
                'image',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Product Image',
                    'attr' => [
                        'maxlength' => 255
                    ]
                ]
            )

            // ->add(
            //     'image',
            //     FileType::class,
            //     [
            //         'label' => 'Product image',
            //         'data_class' => null,
            //         'required' => is_null($builder->getData()->getImage())
            //         //TH1: Book đã có image thì không yêu cầu upload file ảnh mới
            //         //getImage() != null => required = false
            //         //TH2: Book chưa có image thì yêu cầu upload file ảnh
            //         //getImage() = null => required = true    
            //     ]
            // )

            ->add(
                'category',
                EntityType::class,
                [
                    'required' => true,
                    'label' => 'Product category',
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false
                ]
            )
            ->add(
                'info',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Product Information'
                ]
            )
            ->add(
                'quantity',
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'Product Quantity',
                    'attr' => [
                        'min' => 1,
                        'max' => 100
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }
}
