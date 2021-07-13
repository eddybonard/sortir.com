<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom *'
            ])
            ->add('prenom', TextType::class,[
                'label'=>'Prenom *'
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email *'
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Telephone *'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identique',
                'first_options' => [
                    'label' => 'Mot de passe *'
                ],
                'second_options' => [
                    'label'=>'Confirmation mot de passe *'
                ],
                'label' => false,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe requis',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Mot de passe trop court ! 6 caractères minimum',
                        'maxMessage' => 'Mot de passe trop long ! 4096 caractères maximum',
                        'max' => 4096,
                    ]),
                ],
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
