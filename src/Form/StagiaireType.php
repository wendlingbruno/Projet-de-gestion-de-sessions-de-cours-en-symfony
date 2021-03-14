<?php

namespace App\Form;

use App\Entity\Sessions;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StagiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('dateNaissance', DateType::class,[
                "widget" => 'single_text',
                'label' => 'Date de naissance',
                'constraints' => [
                    new LessThan([
                        'value' => 'now',
                        'message' => 'La date doit être inférieure à celle du jour.'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez entrer une date',
                    ]),
                ],
            ])
            ->add('email', EmailType::class)
            ->add('ville', TextType::class)
            ->add('telephone', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 10,
                        'maxMessage' => '{{ limit }} caractère maximum'
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => 'chiffre uniquement'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez entrer un email',
                    ]),
                ],
            ])
            ->add('sexe', ChoiceType::class, array(
                'choices' => array(
                    'Homme' => 'H',
                    'Femme' => 'F',
                ),
                'expanded' => true))
            // ->add('sessions', EntityType::class, [
            //     'class' => Stagiaire::class,
            //     'choice_label' => 'nom'
            // ])
            ->add('Valider', SubmitType::class)
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
