<?php

namespace App\Form;

use App\Entity\Commentaires;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', IntegerType::class, $this->getConfiguration("Note sur 5", "Veuillez indiquer votre note de 0 à 5",[
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'step' => 1
                ]
            ]))
            ->add('contenu', TextareaType::class, $this->getConfiguration("Votre avis / témoignage", "N'hésitez pas à être très précis dans votre commentaire"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}