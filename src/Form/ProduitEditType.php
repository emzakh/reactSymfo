<?php

namespace App\Form;

use App\Entity\Produits;


use App\Entity\Recettes;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;



class ProduitEditType extends ApplicationType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom",
                'attr' => [
                    'placeholder'=>'Nom du produit'
                ]
            ])
            ->add('nomlatin', TextType::class, $this->getConfiguration('Nom Latin', 'Nom latin du produit'))
            ->add('categorie',ChoiceType::class,[
                'choices' => [
                    'Jardin'=>'Jardin',
                    'Potager'=>'Potager',
                    'Epices'=>'Epices'
                ]
            ])
            ->add('effets', TextType::class, $this->getConfiguration('Effets','Donnez une description globale du produit'))
            ->add('description', TextareaType::class, $this->getConfiguration('L\'avis d\'Hildegarde','Description du produit'))
            ->add('image', FileType::class, [
                'label' => "Image de la recette (jpg, png, gif)",
                'data_class'=>null,
                'required'=> false,
            ])
         ->add('recettesAssociees',  EntityType::class, array(
             'class' => Recettes::class,
             'choice_label' => 'titre',
             'required' => false,
             'expanded'  => false,
             'multiple' => true,
             'query_builder' => function (EntityRepository $er){
                 return $er->createQueryBuilder('t')
                     ->orderBy('t.titre', 'ASC');
             },
             'by_reference' => false,
             'attr' => [
                 'class' => 'select-tags'
             ]
         ))



        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }

}