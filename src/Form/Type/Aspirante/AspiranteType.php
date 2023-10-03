<?php


namespace App\Form\Type\Aspirante;


use App\Entity\Aspirante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AspiranteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroIdentificacion',NumberType::class,['required' => true,'label' => 'numero identificacion:'])
            ->add('libretaMilitar',TextType::class,['required' => false ,'label' => 'Libreta militar:'])
            ->add('nombre1',TextType::class,['required' => true,'label' => 'Primer nombre:'])
            ->add('nombre2',TextType::class,['required' => false,'label' => 'Segundo nombre:'])
            ->add('apellido1',TextType::class,['required' => true,'label' => 'Primer apellido:'])
            ->add('apellido2',TextType::class,['required' => false,'label' => 'Segundo apellido:'])
            ->add('telefono',TextType::class,['required' => true,'label' => 'Telefono:'])
            ->add('celular',TextType::class,['required' => true,'label' => 'Celular:'])
            ->add('direccion',TextType::class,['required' => true,'label' => 'Direccion:'])
            ->add('barrio',TextType::class,['required' => true,'label' => 'Barrio:'])
            ->add('correo',TextType::class,['required' => true,'label' => 'Correo:'])
            ->add('fechaNacimiento', DateType::class,array('widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => array('class' => 'date',)))
            ->add('peso',TextType::class,['required' => false,'label' => 'Peso:'])
            ->add('estatura',TextType::class,['required' => false,'label' => 'Estatura:'])
            ->add('cargoAspira',TextType::class,['required' => false,'label' => 'Cargo aspira:'])
            ->add('moto', CheckboxType::class, array('required'  => false))
            ->add('carro', CheckboxType::class, array('required'  => false))
            ->add('posibilidadViajar', CheckboxType::class, array('required'  => false))
            ->add('licenciaCarro', CheckboxType::class, array('required'  => false))
            ->add('licenciaMoto', CheckboxType::class, array('required'  => false))
            ->add('discapacidad', CheckboxType::class, array('required'  => false))
            ->add('cabezaHogar', CheckboxType::class, array('required'  => false))
            ->add('padreFamilia', CheckboxType::class, array('required'  => false))
            ->add('cursoVigilancia', CheckboxType::class, array('required'  => false))
            ->add('fechaVenceCursoVigilancia', DateType::class,array('required'  => false, 'widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => array('class' => 'date',)))
            ->add('numeroHijos', NumberType::class, array('required'  => false, 'data'=>0))
            ->add('ultimaEmpresalabora', TextType::class, array('required'  => false))
            ->add('guardar', SubmitType::class, ['label'=>'Guardar','attr' => ['class' => 'btn btn-sm btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Aspirante::class,
        ]);
    }
}