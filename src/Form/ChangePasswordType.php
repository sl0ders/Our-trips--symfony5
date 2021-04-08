<?php


namespace App\Form;


use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'constraints.user.password.repeatIinvalid',
                'options' => ['attr' => ['class' => 'password-field form-case']],
                'required' => true,
                'first_options' => ['label' => 'form.user.label.password'],
                'second_options' => ['label' => 'form.user.label.repeatPassword'],
            ])->add("submit", SubmitType::class, [
                "label" => "form.label.submit"
            ])
            ->add("return", ButtonType::class, [
                "label" => "form.user.label.returnbutton"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "OurTripsTrans",
            "attr" => ["class" => "form-box"]
        ]);
    }
}
