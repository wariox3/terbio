<?php


namespace App\Controller\Aplicacion\Cliente\Crm;


use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CotizacionController extends AbstractController
{
    #[Route("/cliente/crm/contratacion/lista", name:"cliente_crm_contratacion_lista")]
    public function lista(Request $request, PaginatorInterface $paginator)
    {
        $arUsuario = $this->getUser();
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $url = "/crm/api/comercial/cotizacion/lista";
        $form = $this->createFormBuilder()
            ->getForm();
        if ($form->isSubmitted() && $form->isValid()) {
        }
        $arrCotizaciones = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/cliente/crm/cotizacion/lista.html.twig', [
            'arrCotizaciones' => $arrCotizaciones,
            'form' => $form->createView()
        ]);
    }

    #[Route("/cliente/crm/contratacion/nuevo/{codigoCotizacion}", name:"cliente_crm_contratacion_nuevo")]
    public function nuevo(Request $request, PaginatorInterface $paginator, $codigoCotizacion)
    {
        $urlCapacitacionesTipo = "/crm/api/comercial/cotizaciontipo/lista";
        $arUsuario = $this->getUser();
        $parametros = [
            'codigoClase' => "VIG"
        ];
        $arrCotizaciontipos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $urlCapacitacionesTipo);
        $form = $this->createFormBuilder()
            ->add('estrato', NumberType::class, array('required' => false, 'data'=>3))
            ->add('comentarios', TextareaType::class, array('required' => false, 'attr' => ['style' => 'height:200px']))
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametrosCotizacionNuevo = [
                    'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                    'estrato' => $form['estrato']->getData(),
                    'comentarios' => $form['comentarios']->getData(),
                    'codigoSector' => $request->request->get('cotizacion_servicio_sector'),
                    'cotizacionTipo' => $request->request->get('cotizacion_servicio_tipo'),
                ];
                $urlCapacitacionNuevo = "/crm/api/comercial/cotizacion/nuevo";
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosCotizacionNuevo, $urlCapacitacionNuevo);
                if ($respuesta->error == 0) {
                    return $this->redirect($this->generateUrl('cliente_crm_contratacion_detalle', ['codigoCotizacion' => $respuesta->id]));
                } else {
                    Mensajes::error($respuesta['mensaje']);
                }
            }
        }
        return $this->render('aplicacion/cliente/crm/cotizacion/nuevo.html.twig', [
            'form' => $form->createView(),
            'arrCotizaciontipos' => $arrCotizaciontipos
        ]);
    }

    #[Route("/cliente/crm/contratacion/detalle/{codigoCotizacion}", name:"cliente_crm_contratacion_detalle")]
    public function detalle(Request $request, $codigoCotizacion)
    {
        $arUsuario = $this->getUser();
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
            'codigoCotizacion' => $codigoCotizacion
        ];
        $form = $this->createFormBuilder()
            ->add('btnEliminar', SubmitType::class, ['label' => 'Eliminar', 'disabled' => false, 'attr' => ['class' => 'btn btn-sm btn-danger']] )
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnEliminar')->isClicked()) {
                $parametros['arrDetallesSeleccionados'] = $request->request->get('ChkSeleccionarDetalles');
                FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros,  "/crm/api/comercial/cotizacion/detalle/eliminar");
            }
        }
        $arrCotizacion = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros,  "/crm/api/comercial/cotizacion/detalle");
        $arrCotizacionDetalles = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/crm/api/comercial/cotizacion/detalle/lista");

        return $this->render('aplicacion/cliente/crm/cotizacion/detalle.html.twig', [
            'arCotizacion' => $arrCotizacion,
            'arrCotizacionDetalles'=>$arrCotizacionDetalles,
            'form' => $form->createView()
        ]);
    }

    #[Route("/cliente/crm/contratacion/detalle/nuevo/{id}/{codigoCotizacion}", name:"cliente_crm_contratacion_detalle_nuevo")]
    public function detalleNuevo(Request $request, $id, $codigoCotizacion)
    {
        $arUsuario = $this->getUser();
        $codigoItem = $arUsuario->getEmpresaRel()->getCodigoItem();
        $fechaActual =  new \DateTime('Now');
        $form = $this->createFormBuilder()
            ->add('cantidad', NumberType::class, ['data'=>1])
            ->add('fechaDesde', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'data'=>$fechaActual,'attr' => array('class' => 'date')])
            ->add('fechaHasta', DateType::class, ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'data'=>$fechaActual,'attr' => array('class' => 'date')])
            ->add('lunes', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('martes', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('miercoles', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('jueves', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('viernes', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('sabado', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('domingo', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('festivo', CheckboxType::class, array('required' => false, 'data'=>true))
            ->add('horaDesde', TimeType::class, array('widget' => 'single_text', 'data' => new \DateTime('00:00:00')))
            ->add('horaHasta', TimeType::class, array('widget' => 'single_text', 'data' => new \DateTime('00:00:00')))
            ->add('guardar', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('guardar')->isClicked()) {
                if($codigoItem){
                    $parametrosCotizacionDetalleNuevo = [
                        'codigoCotizacion' => $codigoCotizacion,
                        'fechaDesde' => $form['fechaDesde']->getData(),
                        'fechaHasta' => $form['fechaHasta']->getData(),
                        'horaDesde' => $form['horaDesde']->getData(),
                        'horaHasta' => $form['horaHasta']->getData(),
                        'lunes' => $form['lunes']->getData(),
                        'martes' => $form['martes']->getData(),
                        'miercoles' => $form['miercoles']->getData(),
                        'jueves' => $form['jueves']->getData(),
                        'viernes' => $form['viernes']->getData(),
                        'sabado' => $form['sabado']->getData(),
                        'domingo' => $form['domingo']->getData(),
                        'festivo' => $form['festivo']->getData(),
                        'cantidad' => $form['cantidad']->getData(),
                        'codigoModalidad' => $request->request->get('cotizacion_detalle_modalidad'),
                        'codigoItem' => $codigoItem,
                    ];
                    $urlCapacitacionDetalleNuevo = "/crm/api/comercial/cotizacion/detalle/nuevo";
                    $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosCotizacionDetalleNuevo, $urlCapacitacionDetalleNuevo);
                    if ($respuesta->error == 0) {
                        echo "<script languaje='javascript' type='text/javascript'>window.close();window.opener.location.reload();</script>";
                    } else {
                        Mensajes::error($respuesta['mensaje']);
                    }
                } else{
                    Mensajes::error("La empresa no tiene Código de item de cotizar");
                }
            }
        }

        return $this->render('aplicacion/cliente/crm/cotizacion/detalleNuevo.html.twig', [
            'form' => $form->createView()
        ]);
    }

}