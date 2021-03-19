<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => "form.user.label.email",
                "attr" => ["class" => "form-case"]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'constraints.user.password.repeatIinvalid',
                'options' => ['attr' => ['class' => 'password-field form-case']],
                'required' => true,
                'first_options'  => ['label' => 'form.user.label.password'],
                'second_options' => ['label' => 'form.user.label.repeatPassword'],
            ])
            ->add('firstname', TextType::class, [
                "label" => "form.user.label.firstname",
                "attr" => ["class" => "form-case"]
            ])
            ->add('lastname', TextType::class, [
                "label" => "form.user.label.lastname",
                "attr" => ["class" => "form-case"]
            ])
        ->add("submit", SubmitType::class, [
            "label" => "form.label.submit"
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            "translation_domain" => "OurTripsTrans",
            "attr" => ["class" => "form-box"]
        ]);
    }
}
