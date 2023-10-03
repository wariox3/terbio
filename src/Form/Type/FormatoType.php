<?php

namespace App\Form\Type;

use App\Entity\Formato;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormatoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,['label' => 'Nombre:','required' => true])
            ->add('fecha', DateType::class,array('widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => array('class' => 'date')))
            ->add('fechaActualizacion', DateType::class,array('widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => array('class' => 'date')))
            ->add('version',TextType::class,['label' => 'Version:','required' => true])
            ->add('codigo',TextType::class,['label' => 'Version:','required' => true])
            ->add('contenido',TextareaType::class,['label' => 'Contenido:','required' => true, 'attr'=>['rows'=>'44', 'cols' => '80', 'style'=>'height: 500px;']])
            ->add('guardar', SubmitType::class, ['label'=>'Guardar','attr' => ['class' => 'btn btn-sm btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formato::class,
        ]);
    }

}
