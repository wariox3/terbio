<?php


namespace App\Form\Type\Seguridad\Usuario;


use App\Entity\Identificacion;
use App\Entity\Usuario;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identificacionRel', EntityType::class, [
                'required' => true,
                'class' => Identificacion::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'label' => 'Tipo documento:',
                'attr' => ['class' => 'form-control']
            ])
            ->add('numeroIdentificacion', NumberType::class, ['required' => true, 'label' => 'Número identificación'])
            ->add('nombres', TextType::class, ['required' => true, 'label' => 'Nombre', 'attr' => ['class' => 'form-control']])
            ->add('apellidos', TextType::class, ['required' => false, 'label' => 'Apellidos', 'attr' => ['class' => 'form-control']])
            ->add('correo', EmailType::class, ['required' => true, 'label' => 'Correo electrónico', 'attr' => ['class' => 'form-control']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-primary btn-block']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}