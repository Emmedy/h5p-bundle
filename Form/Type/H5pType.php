<?php

namespace Emmedy\H5PBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class H5pType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('library', HiddenType::class)
            ->add('parameters', HiddenType::class)
            ->add('description', TextareaType::class)
            ->add('save', SubmitType::class, array('label' => 'Save'));
    }
}