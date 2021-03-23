<?php

namespace App\Form;

use App\Entity\News;
use App\Entity\Picture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                "attr" => ["class" => "form-case"],
                "label" => "form.news.label.title"
            ])
            ->add('content', TextareaType::class, [
                "attr" => ["class" => "form-case"],
                "label" => "form.news.label.content"
            ])
            ->add('archived', CheckboxType::class, [
                "attr" => ["class" => "form-case checkbox"],
                "label" => "form.news.label.archived",
                "required" => false
            ])
            ->add('link', EntityType::class, [
                "class" => Picture::class,
                "choice_label" => "picture.name",
                "attr" => ["class" => "form-case"],
                "label" => "form.news.label.linked",
                "required" => false
            ])
            ->add("addPicture", PictureType::class, [
                "label" => "form.news.label.addPicture",
                "attr" => ["class" => "form-case"],
                "required" => false,
                "mapped" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
            "translation_domain" => "OurTripsTrans",
            "attr" => ["class" => "form-box"]
        ]);
    }
}
