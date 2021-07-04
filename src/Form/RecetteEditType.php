<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Recettes;
use App\Entity\Type;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecetteEditType extends AbstractType
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
            ->add('description', TextareaType::class)
            ->add('etapes', TextareaType::class)


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
                'data_class'=>null,
                'required'=>false,
            ]);


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recettes::class,

        ]);
    }



}
