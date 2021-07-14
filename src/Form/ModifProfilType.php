<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\File;

class ModifProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class,[
                'label' => 'Pseudo'
            ])
            ->add('nom', TextType::class,[
                'label' => 'Nom'
            ])
            ->add('prenom', TextType::class,[
                'label' => 'Prenom'
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email'
            ])
            ->add('telephone', TelType::class,[
                'label' => 'Telephone'
            ])
            ->add('photo', FileType::class,[
                'label' => 'Photo (.jpg, .gif, .png)',
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
            ->add('campus', EntityType::class,[
                'class'=> Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('password', PasswordType::class,[
                'constraints' => [
                    new UserPassword([
                        'message' => 'Mot de passe un correct'
                    ])
                ],
                'label' => 'Mot de passe actuel *',
                'invalid_message' => 'Mot de passe incorrect'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
