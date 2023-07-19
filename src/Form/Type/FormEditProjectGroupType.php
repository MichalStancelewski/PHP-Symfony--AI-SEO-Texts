<?php

namespace App\Form\Type;

use App\Form\FormEditProjectGroup;
use App\Form\Loader\ChoiceLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormEditProjectGroupType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nazwa grupy domen',
                    'sanitize_html' => false
                ]
            )
            ->add('projects', ChoiceType::class, [
                    'label' => 'Projekty',
                    'placeholder' => '',
                    'choice_loader' => new ChoiceLoader($this->entityManager, 'App\Entity\Project'),
                    'multiple' => true,
                    'expanded' => false,
                    'required' => false
                ]
            )
            ->add('domainGroup', ChoiceType::class, [
                    'label' => 'Grupy domen',
                    'placeholder' => '',
                    'choice_loader' => new ChoiceLoader($this->entityManager, 'App\Entity\DomainGroup'),
                    'multiple' => false,
                    'expanded' => false,
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
            'data_class' => FormEditProjectGroup::class,
        ]);
    }
}