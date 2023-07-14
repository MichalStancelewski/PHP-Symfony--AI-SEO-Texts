<?php

namespace App\Form\Type;

use App\Form\FormNewDomainGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormNewDomainGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nazwa grupy domen',
                    'sanitize_html' => true
                ]
            )
            ->add('domainsList', TextareaType::class, [
                    'label' => 'Domeny (każda w osobnej linii)',
                    'sanitize_html' => true,
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
            'data_class' => FormNewDomainGroup::class,
        ]);
    }
}