<?php


namespace App\Form\Type\Administracion\Empresa;


use App\Entity\Empresa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add ('nombre', TextType::class, ['required' => true,  'attr'=>['class'=>'form-control']])
            ->add ('direccion', TextType::class, ['required' => true,  'attr'=>['class'=>'form-control']])
            ->add ('telefono', TextType::class, ['required' => true,  'attr'=>['class'=>'form-control']])
            ->add ('nit', TextType::class, ['required' => true,  'attr'=>['class'=>'form-control']])
            ->add ('digitoVerificacion', TextType::class, ['required' => true,  'attr'=>['class'=>'form-control']])
            ->add ('abreviatura', TextType::class, ['required' => true,  'attr'=>['class'=>'form-control']])
            ->add ('urlServicio', UrlType::class, ['required' => true,  'attr'=>['class'=>'form-control']])
            ->add('btnGuardar', SubmitType::class, ['label'=>'Guardar','attr' => ['class' => 'btn btn-sm btn-primary']]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Empresa::class,
        ]);
    }
}