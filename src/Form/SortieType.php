<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' =>'Nom :'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite inscription :',
            'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionMax', NumberType::class, [
                'label' => 'Nombre de places :'
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Dureé : '

            ])
            ->add('infoSortie', TextareaType::class,[
                'label' => 'Description et infos :',
                'attr' => ['style' => 'resize:none;']

            ] )

            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'label' =>'Choix du lieu : ',
                'choice_label' => 'nom'
            ])
            ->add('photo', FileType::class, [
                'label'=> 'Photo (.jpg, .gif, .png)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new  File([
                        'maxSize' => '500000k',
                        'maxSizeMessage' => 'Fichier trop lourd',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',

                        ],
                        'mimeTypesMessage' => 'Format de l\'image non valide'
                    ])
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
