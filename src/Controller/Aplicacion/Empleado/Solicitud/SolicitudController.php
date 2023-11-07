<?php


namespace App\Controller\Aplicacion\Empleado\Solicitud;


use App\Controller\FuncionesController;
use App\Servicios\Correo;
use App\Utilidades\Mensajes;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SolicitudController extends AbstractController
{
    /**
     * @Route("/empleado/solicitud/lista", name="empleado_solicitud_lista")
     */
    public function lista(Request $request, PaginatorInterface $paginator)
    {
        $arUsuario = $this->getUser();
        $parametros = ['identificacion' => $arUsuario->getCodigoIdentificacionFk(), 'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        $form = $this->createFormBuilder()
            ->getForm();
        $arrSolicitudes = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/solicitud/lista");
        return $this->render('aplicacion/empleado/solicitud/lista.html.twig', [
            'arrSolicitudes' => $arrSolicitudes,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/empleado/solicitud/nuevo/{codigoSolicitud}", name="empleado_solicitud_nuevo")
     */
    public function nuevo(Request $request, PaginatorInterface $paginator, $codigoSolicitud)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('fechaDesde', DateTimeType::class, array('required' => true, 'widget' => 'single_text', 'with_seconds' => true, 'data' => new \DateTime('now')))
            ->add('fechaHasta', DateTimeType::class, array('required' => true, 'widget' => 'single_text', 'with_seconds' => true, 'data' => new \DateTime('now')))
            ->add('comentario', TextareaType::class, ['required' => false, 'attr' => ['rows' => 20, 'style' => 'height: 200px;']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametrosSolicitudNuevo = [
                    'identificacion' => $arUsuario->getCodigoIdentificacionFk(),
                    'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(),
                    'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d H:i:s'),
                    'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d H:i:s'),
                    'comentario' => $form['comentario']->getData(),
                    'solicitudTipo' => $request->request->get('solicitudTipo'),
                ];
                $urlSolicitudNuevo = "/recursohumano/api/solicitud/nuevo";
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosSolicitudNuevo, $urlSolicitudNuevo);
                if ($respuesta->error == 0) {
                    $correo = new Correo();
                    $asunto = "Se ha registrado con éxito la solicitud";
                    $respuestaCorreo = $correo->enviarCorreo($arUsuario->getCorreo(), $asunto,
                        $this->renderView(
                            'aplicacion/seguridad/correoNotificacionSolicitud.html.twig',
                            array(
                                'nombreEmpresa' => $arUsuario->getEmpresaRel()->getNombre(),
                                'id'=> $respuesta->id
                            )
                        ));
                    if ($respuestaCorreo->error === false) {
                        return $this->redirect($this->generateUrl('empleado_solicitud_lista'));
                    } else {
                        Mensajes::error("Error al enviar correo de confirmación de registro de la solicitud{$respuestaCorreo->mensajeError}");
                    }
                } else {
                    Mensajes::error($respuesta['mensaje']);
                }
            }
        }
        $arrSolicitudTipos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [''], "/recursohumano/api/solicitudempleadotipo/lista");
        return $this->render('aplicacion/empleado/solicitud/nuevo.html.twig', [
            'form' => $form->createView(),
            'arrSolicitudEmpleadoTipos' => $arrSolicitudTipos,
        ]);
    }

    /**
     * @Route("/empleado/solicitud/detalle/{codigoSolicitud}", name="empleado_solicitud_detalle")
     */
    public function detalle(Request $request, PaginatorInterface $paginator, $codigoSolicitud)
    {

    }

}