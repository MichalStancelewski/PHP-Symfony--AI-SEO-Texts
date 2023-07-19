<?php

namespace App\Form\Type;

use App\Form\FormNewProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormNewProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nazwa projektu',
                    'sanitize_html' => false
                ]
            )
            ->add('theme', TextType::class, [
                    'label' => 'Temat tekstów',
                    'sanitize_html' => false
                ]
            )
            ->add('numberOfArticles', IntegerType::class, [
                    'label' => 'Liczba tekstów'
                ]
            )
            ->add('textsLength', IntegerType::class, [
                    'label' => 'Długość tekstu'
                ]
            )
            ->add('withTitle', ChoiceType::class, [
                'label' => 'Nadać tytuł?',
                'choices' => [
                    'Tak' => true,
                    'Nie' => false
                ],
                'expanded' => true
            ])
            ->add('language', LanguageType::class, [
                    'label' => 'Język',
                    'alpha3' => true,
                    'choice_loader' => null,
                    'choice_translation_locale' => 'pol',
                    'choices' => array(
                        'angielski' => 'eng',
                        'czeski' => 'cze',
                        'francuski' => 'fre',
                        'hiszpański' => 'spa',
                        'holenderski' => 'dut',
                        'niemiecki' => 'ger',
                        'rosyjski' => 'rus',
                        'polski' => 'pol',
                        'ukraiński' => 'ukr',
                        'włoski' => 'ita'
                    ),
                    'preferred_choices' => array('polski', 'pol')
                ]
            )
            ->add('cardLinkCoverage', ChoiceType::class, [
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
            'data_class' => FormNewProject::class,
        ]);
    }
}