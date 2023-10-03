<?php


namespace App\Form\Type\Aspirante;


use App\Entity\Estudio;
use App\Entity\RecursoHumano\RhuEstudioEstado;
use App\Entity\RecursoHumano\RhuEstudioTipo;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstudioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('institucion', TextType::class, array('required' => true))
            ->add('duracionEstudio', TextType::class, array('required' => false, 'attr'=>['placeholder'=> 'ejemplos: 5 años, 8 semestres, 48 horas']))
            ->add('fechaInicio', DateType::class, array('attr' => array(),  'widget' => 'single_text', 'format' => 'yyyy-MM-dd','required' => true, 'years' => range(date('1970'), date('Y') + 5)))
            ->add('fechaTerminacion', DateType::class, array('required' => false,  'widget' => 'single_text', 'format' => 'yyyy-MM-dd','years' => range(date('1970'), date('Y') + 5)))
            ->add('titulo', TextType::class, array('required' => true))
            ->add('guardar', SubmitType::class, ['attr' => ['class' => 'btn btn-sm btn-primary']]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Estudio::class,
        ]);
    }
}