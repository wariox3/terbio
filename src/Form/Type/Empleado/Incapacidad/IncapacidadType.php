<?php


namespace App\Form\Type\Empleado\Incapacidad;


use App\Entity\Incapacidad;
use App\Entity\IncapacidadTipo;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncapacidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('incapacidadTipoRel', EntityType::class, [
                'required' => true,
                'class' => IncapacidadTipo::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('it')
                        ->orderBy('it.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'label' => 'Incapacidad tipo:',
                'attr' => ['class' => 'aplicarSelect2']
            ])
            ->add('numeroEps', TextType::class, array('required' => true))
            ->add('fechaDesde', DateType::class, ['required' => true, 'widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => ['class' => 'date',]])
            ->add('fechaHasta', DateType::class, ['required' => true, 'widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => ['class' => 'date',]])
            ->add('comentarios', TextareaType::class, ['required' => false])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Incapacidad::class,
        ]);
    }
}