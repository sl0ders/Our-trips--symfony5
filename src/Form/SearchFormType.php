<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', EntityType::class, [
                'label' => "form.label.country",
                'required' => false,
                'class' => Country::class,
                'expanded' => true,
                'multiple' => true,
                "attr" => ["class" => "form-case"]
            ])
            ->add('city', EntityType::class, [
                'label' => "form.label.city",
                'required' => false,
                'class' => City::class,
                'expanded' => true,
                'multiple' => true,
                "attr" => ["class" => "form-case"]
            ])
            ->add("submit", SubmitType::class, [
                "label" => "form.picture.label.search"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "attr" => ["class" => "form-check"],
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            "translation_domain" => "OurTripsTrans"
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
