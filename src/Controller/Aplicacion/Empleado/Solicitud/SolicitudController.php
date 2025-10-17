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
    #[Route("/empleado/solicitud/lista", name:"empleado_solicitud_lista")]
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

    #[Route("/empleado/solicitud/nuevo/{codigoSolicitud}", name:"empleado_solicitud_nuevo")]
    public function nuevo(Request $request, $codigoSolicitud = null)
    {
        $arUsuario = $this->getUser();
        $esEdicion = ($codigoSolicitud != null);
        $datosIniciales = [
            'fechaDesde' => new \DateTime('now'),
            'fechaHasta' => new \DateTime('now'),
            'comentario' => ''
        ];
        if ($esEdicion) {
            $parametrosConsulta = [
                'identificacion' => $arUsuario->getCodigoIdentificacionFk(),
                'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(),
                'codigoSolicitud' => $codigoSolicitud
            ];

            $respuestaConsulta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosConsulta, "/recursohumano/api/solicitud/consulta");

            if ($respuestaConsulta->error == 0 && isset($respuestaConsulta->solicitud)) {
                $solicitud = $respuestaConsulta->solicitud;
                $datosIniciales = [
                    'fechaDesde' => new \DateTime($solicitud->fechaDesde),
                    'fechaHasta' => new \DateTime($solicitud->fechaHasta),
                    'comentario' => $solicitud->comentario
                ];
            } else {
                Mensajes::error("No se encontró la solicitud a editar");
                return $this->redirect($this->generateUrl('empleado_solicitud_lista'));
            }
        }

        $form = $this->createFormBuilder($datosIniciales)
            ->add('fechaDesde', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'with_seconds' => true
            ])
            ->add('fechaHasta', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'with_seconds' => true
            ])
            ->add('comentario', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 20, 'style' => 'height: 200px;']
            ])
            ->add('btnGuardar', SubmitType::class, [
                'label' => $esEdicion ? 'Actualizar' : 'Guardar',
                'attr' => ['class' => 'btn btn-sm btn-primary']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametrosSolicitud = [
                    'identificacion' => $arUsuario->getCodigoIdentificacionFk(),
                    'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(),
                    'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d H:i:s'),
                    'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d H:i:s'),
                    'comentario' => $form['comentario']->getData(),
                    'solicitudTipo' => $request->request->get('solicitudTipo'),
                ];

                if ($esEdicion) {
                    $parametrosSolicitud['codigoSolicitud'] = $codigoSolicitud;
                }

                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosSolicitud, "/recursohumano/api/solicitud/nuevo");

                if ($respuesta->error == 0) {
                    $correo = new Correo();
                    $accion = $esEdicion ? "actualizado" : "registrado";
                    $asunto = "Se ha {$accion} con éxito la solicitud";

                    $respuestaCorreo = $correo->enviarCorreo($arUsuario->getCorreo(), $asunto,
                        $this->renderView(
                            'aplicacion/seguridad/correoNotificacionSolicitud.html.twig',
                            array(
                                'nombreEmpresa' => $arUsuario->getEmpresaRel()->getNombre(),
                                'id' => $respuesta->id
                            )
                        ), $arUsuario->getCodigoEmpresaFk());

                    if ($respuestaCorreo->error === false) {
                        return $this->redirect($this->generateUrl('empleado_solicitud_lista'));
                    } else {
                        Mensajes::error("Error al enviar correo: {$respuestaCorreo->mensajeError}");
                    }
                } else {
                    Mensajes::error($respuesta->mensaje);
                }
            }
        }

        $arrSolicitudTipos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [''], "/recursohumano/api/solicitudempleadotipo/lista");

        // Obtener el tipo de solicitud actual si es edición
        $solicitudTipoActual = null;
        if ($esEdicion && isset($solicitud)) {
            $solicitudTipoActual = $solicitud->codigoSolicitudEmpleadoTipoFk;
        }

        return $this->render('aplicacion/empleado/solicitud/nuevo.html.twig', [
            'form' => $form->createView(),
            'arrSolicitudEmpleadoTipos' => $arrSolicitudTipos,
            'esEdicion' => $esEdicion,
            'codigoSolicitud' => $codigoSolicitud,
            'solicitudTipoActual' => $solicitudTipoActual
        ]);
    }

    #[Route("/empleado/solicitud/detalle/{codigoSolicitud}", name:"empleado_solicitud_detalle")]
    public function detalle(Request $request, PaginatorInterface $paginator, $codigoSolicitud)
    {

    }

}