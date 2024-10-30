<?php


namespace App\Controller\Aplicacion\Empleado\Programacion;


use App\Controller\FuncionesController;
use App\Servicios\Correo;
use App\Utilidades\Mensajes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProgramacionController extends AbstractController
{
    #[Route("empleado/programacion/lista", name:"turno_programacion_lista")]
    public function lista(Request $request)
    {
        $arrFechas = $this->fechasProgramacion();
        $arUsuario = $this->getUser();
        $parametros = [
            'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(),
            'fechas' => $arrFechas
        ];
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        $arrProgramaciones = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/turno/api/programacion/numeroIdentificacion", true);
        return $this->render('aplicacion/turno/programacion/lista.html.twig', [
            'arrProgramaciones' => $arrProgramaciones,
            'form' => $form->createView(),
        ]);
    }

    private function fechasProgramacion()
    {
        $arrFecha = [];
        $fecha = new \DateTime('now');

        //Mes siguiente
        $anio = $fecha->format('Y');
        $mes = $fecha->format('n');
        if ($mes == 12) {
            $mes = 1;
            $anio += 1;
        } else {
            $mes += 1;
        }
        $arrFecha[] = ['anio' => $anio, 'mes' => $mes, 'actual' => '0'];
        //Mes actual
        $arrFecha[] = ['anio' => $fecha->format('Y'), 'mes' => $fecha->format('n'), 'actual' => '1'];
        //Mes anterior
        $anio = $fecha->format('Y');
        $mes = $fecha->format('n');
        if ($mes == 1) {
            $mes = 12;
            $anio -= 1;
        } else {
            $mes -= 1;
        }
        $arrFecha[] = ['anio' => $anio, 'mes' => $mes, 'actual' => '0'];
        return $arrFecha;
    }

    #[Route("empleado/programacion/reportar/{codigoProgramacion}", name:"turno_programacion_reportar")]
    public function reportar(Request $request, $codigoProgramacion)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('diaHasta', IntegerType::class, array('required' => true))
            ->add('diaDesde', IntegerType::class, array('required' => true))
            ->add('comentario', TextareaType::class, ['required' => true, 'attr' => ['class' => 'form-control', 'maxlength'=>'500']])
            ->add('btnReportar', SubmitType::class, ['label' => 'Reportar', 'attr' => ['class' => 'btn btn-default btn-sm']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnReportar')->isClicked()) {
                $parametros = [
                    'codigoProgramacion' => $codigoProgramacion,
                    'diaDesde' => $form->get('diaDesde')->getData(),
                    'diaHasta' => $form->get('diaHasta')->getData(),
                    'reporteTipo' => $request->request->get('reporteTipo'),
                    'comentario' => $form->get('comentario')->getData()
                ];
                $programacionReporte = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/turno/api/programacionreporte/nuevo");
                if ($programacionReporte) {
                    $correo = new Correo();
                    $asunto = "Se ha registrado con éxito el reporte de programación";
                    $respuestaCorreo = $correo->enviarCorreo($arUsuario->getCorreo(), $asunto,
                        $this->renderView(
                            'aplicacion/seguridad/correoNotificacionProgramacionReporte.html.twig',
                            array(
                                'nombreEmpresa' => $arUsuario->getEmpresaRel()->getNombre(),
                                'id' => $programacionReporte->id
                            )
                        ), $arUsuario->getCodigoEmpresaFk());
                    if ($respuestaCorreo->error === false) {
                        return $this->redirect($this->generateUrl('turno_programacion_lista'));
                    } else {
                        Mensajes::error("Error al enviar correo de confirmación de registro de reporte{$respuestaCorreo->mensajeError}");
                    }

                } else {
                    Mensajes::info("error al actualizar");
                }

            }
        }
        $arrProgramacionReporteTipos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [''], "/turno/api/programacionreportetipo/lista");
        return $this->render('aplicacion/turno/programacion/reportar.html.twig', [
            'codigoProgramacion' => $codigoProgramacion,
            'arrProgramacionReporteTipos' => $arrProgramacionReporteTipos,
            'form' => $form->createView(),
        ]);
    }
}