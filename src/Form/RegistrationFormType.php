<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'required' => true,
                'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un prénom',
                ])
                ]
            ])
            ->add('lastName', null, [
                'required' => true,
                'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un nom',
                ])
                ]
            ])
            ->add('email', null, [
                'required' => true,
                'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un email',
                ]),
                new Email([
                    'message' => 'Cet email n\'est pas valide',
                    'mode' => 'strict',
                    'normalizer' => 'trim'
                ])
                ]

            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez acceptez les conditions.',
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répétez ce mot de passe'],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au minimum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
