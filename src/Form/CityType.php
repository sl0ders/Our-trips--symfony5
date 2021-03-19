<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "form.city.label.name",
                "attr" => ["class" => "form-case"]
            ])
            ->add('description', TextareaType::class, [
                "label" => "form.city.label.description",
                "attr" => ["class" => "form-case"]
            ])
            ->add('country', EntityType::class, [
                "label" => "form.city.label.country",
                "choice_label" => "name",
                "class" => Country::class,
                "attr" => ["class" => "form-case"]
            ])
            ->add('mapFile', VichFileType::class, [
                "label" => "form.city.label.map",
                "attr" => ["class" => "form-case"]
            ])
            ->add('iconFile', VichFileType::class, [
                "label" => "form.city.label.icon",
                "attr" => ["class" => "form-case"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
            "translation_domain" => "OurTripsTrans",
            "attr" => ["class" => "form-box"]
        ]);
    }
}