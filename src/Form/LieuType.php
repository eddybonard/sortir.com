<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 */
class LieuType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ville', EntityType::class,[
                'class' => Ville::class,
                'choice_label' =>'nom',
                'required' => true
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'required' => true
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue :',
                'required' => true
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude : ',
                'required' => false
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude : ',
                'required' => false
            ])

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
