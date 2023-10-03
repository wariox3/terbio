<?php


namespace App\Form\Type\Aspirante;


use App\Entity\AspiranteIdioma;
use App\Entity\Idioma;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AspiranteIdiomaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idiomaRel', EntityType::class, [
                'class' => Idioma::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.nombre', 'ASC');
                },
                'required' => true,
                'choice_label' => 'nombre',
                'attr' => ["class" => "to-select-2"],
                'placeholder' => ''
            ])
            ->add('nivel', ChoiceType::class, array('choices' =>
                    array('Muy Básico'=>'MuyBasico',
                        'Básico'=>'Basico',
                        'Intermedio'=>'Intermedio',
                        'Avanzado'=>'Avanzado',
                        'Nativo'=>'Nativo',
                    ),'required' => true)
                )
            ->add('guardar', SubmitType::class, ['label'=>'Guardar','attr' => ['class' => 'btn btn-sm btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AspiranteIdioma::class,
        ]);
    }

}