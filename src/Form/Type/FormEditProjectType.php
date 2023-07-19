<?php

namespace App\Form\Type;

use App\Form\FormEditProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormEditProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nazwa projektu'
                ]
            )->add('cardLinkCoverage', ChoiceType::class, [
                'label' => 'Pokrycie linkami',
                'choices' => [
                    '0%' => 0,
                    '25%' => 25,
                    '50%' => 50,
                    '100%' => 100
                ],
                'expanded' => false
            ])
            ->add('cardHeader', TextareaType::class, [
                    'label' => 'Nagłówek',
                    'sanitize_html' => false,
                    'required' => false
                ]
            )
            ->add('cardCompanyName', TextType::class, [
                    'label' => 'Nazwa firmy/strony',
                    'required' => false
                ]
            )
            ->add('cardCompanyPhone', TelType::class, [
                    'label' => 'Telefon',
                    'invalid_message' => 'TELEFON w niepoprawnym formacie!',
                    'required' => false
                ]
            )
            ->add('cardCompanyEmail', EmailType::class, [
                    'label' => 'Email',
                    'invalid_message' => 'EMAIL w niepoprawnym formacie!',
                    'required' => false
                ]
            )
            ->add('cardCompanyWebsite', UrlType::class, [
                    'label' => 'Link do strony',
                    'invalid_message' => 'URL w niepoprawnym formacie!',
                    'required' => false
                ]
            )
            ->add('save', SubmitType::class, [
                    'label' => 'Zapisz'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormEditProject::class,
        ]);
    }
}