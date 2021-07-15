<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'label' => 'Description et infos :'
            ] )
            ->add('campusOrganisateur', EntityType::class, [
                'class' => Campus::class,
                    'choice_label' => 'nom'



            ])
           /* ->add('ville', EntityType::class, [
                'class' =>Ville::class,
                    'choice_label' =>'nom'

            ])*/
            /*->add('lieu', EntityType::class, [
        'class' =>Ville::class, [
            'label' =>'nom'
                    ]
        ])*/
           /* ->add('rue', EntityType::class, [
                'class' =>Ville::class, [
                    'label' =>'nom'
                ])*/
            /*->add('codePostal', EntityType::class, [
                'class' =>Ville::class,
                    'label' =>'code_Postal'

                ])*/
            /*->add('latitude', EntityType::class, [
                'class' =>Ville::class, [
                    'label' =>'nom'
                ])
            ->add('longitude', EntityType::class, [
                'class' =>Ville::class, [
                    'label' =>'nom'
                ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
