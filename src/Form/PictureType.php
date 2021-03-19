<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Picture;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pictureFile', VichFileType::class, [
                "label" => "form.picture.label.download",
                "attr" => ["class" => "form-case"],
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'asset_helper' => false,
            ])
            ->add('description', TextareaType::class, [
                "label" => "form.picture.label.description",
                "attr" => ["class" => "form-case"]
            ])
            ->add('city', EntityType::class, [
                "class" => City::class,
                "label" => "form.picture.label.city",
                "choice_label" => "name",
                "attr" => ["class" => "form-case"]
            ])
            ->add("newCity", CityType::class, [
                "label" => "form.picture.label.addCity",
                "attr" => ["class" => "city-form form-case"],
                "required" => false,
                "mapped" => false
            ])
            ->add('author', EntityType::class, [
                "class" => User::class,
                "label" => "form.picture.label.author",
                "choice_label" => "firstname",
                "attr" => ["class" => "form-case"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            "translation_domain" => "OurTripsTrans",
            "attr" => ["class" => "form-box"]
        ]);
    }
}
