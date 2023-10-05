<?php


namespace App\Form\Type\Administracion\Empresa;


use App\Entity\Identificacion;
use App\Entity\Usuario;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('identificacionRel',EntityType::class,[
                'required' => true,
                'class' => Identificacion::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'label' => 'Tipo documento:',
                'attr'=>['class'=>'form-control']
            ])
            ->add ('numeroIdentificacion', TextType::class, ['required' => true, 'label' => 'Número idenficación', 'attr'=>['class'=>'form-control']])
            ->add ('codigoUsuarioPk', TextType::class, ['required' => true, 'label' => 'Nombre' , 'attr'=>['class'=>'form-control']])
            ->add ('nombres', TextType::class, ['required' => true, 'label' => 'Nombre' , 'attr'=>['class'=>'form-control']])
            ->add ('apellidos', TextType::class, ['required' => false, 'label' => 'Apellidos', 'attr'=>['class'=>'form-control'] ])
            ->add ('correo', EmailType::class, ['required' => true, 'label' => 'Correo electrónico' , 'attr'=>['class'=>'form-control']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr'=>['class'=>'btn btn-sm btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}