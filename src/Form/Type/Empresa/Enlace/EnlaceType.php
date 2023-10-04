<?php


namespace App\Form\Type\Empresa\Enlace;


use App\Entity\Enlace;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('nombre', TextType::class, ['required' => true, 'label' => 'Nombre', 'attr'=>['class'=>'form-control']])
            ->add('enlace', UrlType ::class, ['default_protocol' => 'http://', 'required' => true, 'label' => 'Nombre', 'attr'=>['class'=>'form-control']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr'=>['class'=>'btn btn-sm btn-primary']]);
    }


    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class' => Enlace::class,
        ]);
    }
}