<?php

namespace App\Form;

use App\Entity\Sessions;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SessionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un nom de session',
                    ]),
                ],
            ])
            ->add('dateDebut', DateType::class,[
                "widget" => 'single_text',
                'label' => 'Date de début',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de rentrer une date',
                    ]),
                ],
                ])
            ->add('dateFin', DateType::class,[
                "widget" => 'single_text',
                'label' => 'Date de fin',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de rentrer une date',
                    ]),
                ],])
            ->add('placesMax', IntegerType::class, [
                'required' => true,
                'attr' => array('min' => 1, 'max' => 60),                
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de préciser un chiffre',
                    ]),
                    new Type([
                        'type' => 'int',
                        'message' => 'Merci d\'entrer uniquement des chiffres'
                    ]),
                ],
            ])
        //     ->add('stagiaires', EntityType::class, [
        //         'class' => Stagiaire::class,
        //         'choice_label' => 'nom'
        //     ])
         ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sessions::class,
        ]);
    }
}
