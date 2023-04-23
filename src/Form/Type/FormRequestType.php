<?php

namespace App\Form\Type;

use App\Form\FormRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nazwa projektu'
                ]
            )
            ->add('theme', TextType::class, [
                    'label' => 'Temat tekstów'
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
            ->add('save', SubmitType::class, [
                    'label' => 'Zapisz'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormRequest::class,
        ]);
    }
}