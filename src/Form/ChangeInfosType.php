<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangeInfosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                // 'disabled' => true,
                'label' => 'My Email Address'
            ])
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Actual Password',
                'attr'=> [
                    'placeholder' => "Enter your actual password",
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'New Password:',
                    'attr'=> [
                        'placeholder' => "Enter your new password"
                    ]
                ],
                'second_options' => [
                    'label' => 'New password:',
                    'attr'=> [
                        'placeholder' => "Confirm your new password"
                    ]
                ],
                'invalid_message' => "Passwords are different",
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6])
                ]
            ])
            ->add('nickname', TextType::class, [

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
