<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Recettes;
use App\Entity\Type;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecetteType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Titre', TextType::class, [
                'label' => "Titre",
                'attr' => [
                    'placeholder'=>'Titre de la recette'
                ]
            ])
            ->add('description', TextareaType::class, $this->getConfiguration('Description','Description de la recette'))
            ->add('etapes', TextareaType::class, $this->getConfiguration('Etapes','1. Décrivez les étapes de votre recette...'))


            ->add('ingredients',  EntityType::class, array(
                'class' => Produits::class,
                'choice_label' => 'nom',
                'expanded'  => false,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.nom', 'ASC');
                },
                'by_reference' => false,
                'attr' => [
                    'class' => 'select-tags'
                ]

            ))


            ->add('types',ChoiceType::class,[
                'choices' => [
                    'Plat'=>'Plat',
                    'Dessert'=>'Dessert',
                    'Boisson'=>'Boisson'
                ]
            ])

            ->add('imgRecette', FileType::class, [
            'label' => "Image de la recette (jpg, png, gif)",
                'data_class' => null
    ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recettes::class,
        ]);
    }




}
