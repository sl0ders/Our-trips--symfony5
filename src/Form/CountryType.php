<?php

namespace App\Form;

use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "form.country.label.name",
                "attr" => ["class" => "form-case"]
            ])
            ->add('description', TextareaType::class, [
                "label" => "form.country.label.description",
                "attr" => ["class" => "form-case"]
            ])
            ->add('mapFile', VichFileType::class, [
                "label" => "form.country.label.map",
                "attr" => ["class" => "form-case"]
            ])
            ->add('iconFile', VichFileType::class, [
                "label" => "form.country.label.icon",
                "attr" => ["class" => "form-case"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
            "translation_domain" => "OurTripsTrans",
            "attr" => ["class" => "form-box"]
        ]);
    }
}
