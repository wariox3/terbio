<?php

namespace App\Controller\Aplicacion\Empleado;

use App\Controller\Aplicacion\Usuario\usuarioController;
use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Entity\Usuario;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{

    /**
     * @Route("/empleado", name="empleado_inicio")
     */
    public function inicioAction(Request $request,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        if($arUsuario->getCodigoEmpresaFk() == "0") {
            Mensajes::error("Debe cambiar la empresa para la cual labora");
        }
        $fecha = new \DateTime('now');
        $anio = $fecha->format('Y');
        $mes = $fecha->format('n');
        $dia = $fecha->format('j');
        $parametros = [
            'numeroIdentificacion'=> $arUsuario->getNumeroIdentificacion(),
            'anio' => $anio,
            'mes' => $mes,
            'dia' => $dia
        ];
        $arrTurnos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/turno/api/programacion/turno");
        return $this->render('aplicacion/inicio.html.twig', [
            'arrTurnos' => $arrTurnos
        ]);
    }

    /**
     * @Route("/empleado/cambioempresa/{codigoEmpleado}", name="empleado_cambio_empresa")
     */
    public function cambioEmpresa(Request $request, $codigoEmpleado,  EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->add('empresa', TextType::class, ['attr' => ['placeholder' => 'Escribir nit empresa']])
            ->add('btnCambiar', SubmitType::class, array('label' => 'Cambiar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnCambiar')->isClicked()) {
                $txtEmpresa = $form->get('empresa')->getData();
                $arEmpresa = $em->getRepository(Empresa::class)->findOneBy(['nit' => $txtEmpresa]);
                if ($arEmpresa) {
                    $arUsuario = $em->getRepository(Usuario::class)->find($this->getUser()->getCodigoUsuarioPk());
                    if ($arUsuario) {
                        $parametros = ['tipoIdentificacion' => $arUsuario->getCodigoIdentificacionFk(), 'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
                        $booValidarIdentifiacion = FuncionesController::consumirApi($arEmpresa, $parametros, "/recursohumano/api/empleado/validarIdentifiacion");
                        if ($booValidarIdentifiacion) {
                            $arUsuario->setEmpresaRel($arEmpresa);
                            $arUsuario->setEmpleado(true);
                            $em->persist($arUsuario);
                            $em->flush();
                            return $this->redirect($this->generateUrl('inicio'));
                        } else {
                            Mensajes::warning("El empleado con número de identificación {$arUsuario->getNumeroIdentificacion()} no esta registrado ante la empresa con nit {$txtEmpresa}, por favor validar");
                        }
                    }
                }else{
                    Mensajes::warning("El nit {$txtEmpresa} no se encuenta registrado en el sistema");
                }
            }
            return $this->redirect($this->generateUrl('inicio'));
        }
        return $this->render('aplicacion/empleado/cambioEmpresa/lista.html.twig', [
            'form' => $form->createView(),
            'codigoEmpleado' => $codigoEmpleado
        ]);
    }

}