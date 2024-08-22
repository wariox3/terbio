<?php


namespace App\Controller\Aplicacion\Cliente\Transporte;

use App\Controller\FuncionesController;
use App\Formato\Guias;
use App\Formato\Guias2;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuiaController extends AbstractController
{
    /**
     * @Route("/cliente/transporte/guia/lista", name="cliente_transporte_guia_lista")
     */
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $url = "/transporte/api/oxigeno/guia/lista";
        $urlGuiasTipo = "/transporte/api/oxigeno/guiatipo/lista";
        $arrGuias = [];
        $arrGuiaTipo = [];
        $arGuiaFormato = [];
        $respuestaGuiasTipo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [], $urlGuiasTipo);
        if ($respuestaGuiasTipo != null) {
            if ($respuestaGuiasTipo->error == false) {
                $arrGuiaTipo = $this->fuenteChoiceGuiasTipos($respuestaGuiasTipo->arrGuiaTipo);
                if(isset($respuestaGuiasTipo->arrGuiaFormato)) {
                    $arGuiaFormato = $respuestaGuiasTipo->arrGuiaFormato;
                }
            }
        }
        $arrBotonFormato1 = array('label' => 'Formato 1', 'disabled' => false);
        $arrBotonFormato2 = array('label' => 'Formato 2', 'disabled' => false);
        $arrBotonFormato3 = array('label' => 'Formato 3', 'disabled' => false);
        $arrBotonFormato4 = array('label' => 'Formato 4', 'disabled' => false);
        $arrBotonRotulo1 = array('label' => 'Rotulo 1', 'disabled' => false);

        if($arGuiaFormato) {
            if($arGuiaFormato->formato1 == 0) {
                $arrBotonFormato1['disabled'] = true;
            }
            if($arGuiaFormato->formato2 == 0) {
                $arrBotonFormato2['disabled'] = true;
            }
            if($arGuiaFormato->formato3 == 0) {
                $arrBotonFormato3['disabled'] = true;
            }
            if($arGuiaFormato->formato4 == 0) {
                $arrBotonFormato4['disabled'] = true;
            }
            if($arGuiaFormato->rotulo1 == 0) {
                $arrBotonRotulo1['disabled'] = true;
            }
        }
        $form = $this->createFormBuilder()
            ->add('codigo', IntegerType::class, array('required' => false))
            ->add('ciudadOrigen', IntegerType::class, array('required' => false))
            ->add('ciudadDestino', IntegerType::class, array('required' => false))
            ->add('documentoCliente', TextType::class, array('required' => false))
            ->add('fechaDesde', DateType::class, ['label' => 'Fecha desde: ', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'required' => false, 'data' => new \DateTime('now')])
            ->add('fechaHasta', DateType::class, ['label' => 'Fecha hasta: ', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'required' => false, 'data' => new \DateTime('now')])
            ->add('codigoGuiaTipo', ChoiceType::class, ['choices' => $arrGuiaTipo, 'required' => false])
            ->add('codigoDespacho', IntegerType::class, array('required' => false))
            ->add('limiteRegistros', IntegerType::class, array('required' => false, 'data' => 100))
            ->add('btnFormato1', SubmitType::class, $arrBotonFormato1)
            ->add('btnFormato2', SubmitType::class, $arrBotonFormato2)
            ->add('btnFormato3', SubmitType::class, $arrBotonFormato3)
            ->add('btnFormato4', SubmitType::class, $arrBotonFormato4)
            ->add('btnRotulo1', SubmitType::class, $arrBotonRotulo1)
            ->add('btnExcel', SubmitType::class, array('label' => 'Excel'))
            ->add('btnPdf', SubmitType::class, array('label' => 'Pdf'))
            ->add('btnPdf2', SubmitType::class, array('label' => 'Pdf 2'))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
            'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d'),
            'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d'),
            'limiteRegistros' => 100
        ];
        if ($form->isSubmitted()) {
            if ($form->get('btnFiltro')->isClicked() || $form->get('btnExcel')->isClicked() || $form->get('btnPdf')->isClicked() || $form->get('btnPdf2')->isClicked()) {
                $parametros['codigoGuia'] = $form->get('codigo')->getData();
                $parametros['documentoCliente'] = $form->get('documentoCliente')->getData();
                $parametros['codigoGuiaTipo'] = $form->get('codigoGuiaTipo')->getData();
                $parametros['codigoCiudadOrigen'] = $form->get('ciudadOrigen')->getData();
                $parametros['codigoCiudadDestino'] = $form->get('ciudadDestino')->getData();
                $parametros['codigoDespacho'] = $form->get('codigoDespacho')->getData();
                $parametros['limiteRegistros'] = $form->get('limiteRegistros')->getData();
            }
            if ($form->get('btnRotulo1')->isClicked()) {
                $urlRotulo = "/transporte/api/oxigeno/guia/rotulo";
                $parametrosRotulo = [
                    'formato' => 1,
                    'filtros' => [
                        'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                        'codigoGuiaDesde' => $form->get('codigo')->getData(),
                        'codigoGuiaHasta' => $form->get('codigo')->getData(),
                        'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d'),
                        'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d'),
                        'codigoCiudadOrigen' => $form->get('ciudadOrigen')->getData(),
                        'codigoCiudadDestino' => $form->get('ciudadDestino')->getData(),
                        'codigoDespacho' => $form->get('codigoDespacho')->getData()
                    ]
                ];
                $respuestaRotulo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosRotulo, $urlRotulo);
                if ($respuestaRotulo->error == false) {
                    $archivo = "/var/www/html/temporal/rotulo.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuestaRotulo->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="rotulo.pdf";');
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
            if ($form->get('btnFormato1')->isClicked()) {
                $urlFormato1 = "/transporte/api/oxigeno/guia/imprimir";
                $parametrosFormato1 = [
                    'formato' => 1,
                    'copias' => 1,
                    'filtros' => [
                        'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                        'codigoGuiaDesde' => $form->get('codigo')->getData(),
                        'codigoGuiaHasta' => $form->get('codigo')->getData(),
                        'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d'),
                        'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d'),
                        'codigoCiudadOrigen' => $form->get('ciudadOrigen')->getData(),
                        'codigoCiudadDestino' => $form->get('ciudadDestino')->getData(),
                        'codigoDespacho' => $form->get('codigoDespacho')->getData()
                    ]
                ];
                $respuestaFormato1 = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosFormato1, $urlFormato1);
                if ($respuestaFormato1->error == false) {
                    $archivo = "/var/www/html/temporal/guia.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuestaFormato1->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="guia.pdf";');
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
            if ($form->get('btnFormato2')->isClicked()) {
                $urlFormato2 = "/transporte/api/oxigeno/guia/imprimir";
                $parametrosFormato2 = [
                    'formato' => 2,
                    'copias' => 1,
                    'filtros' => [
                        'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                        'codigoGuiaDesde' => $form->get('codigo')->getData(),
                        'codigoGuiaHasta' => $form->get('codigo')->getData(),
                        'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d'),
                        'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d'),
                        'codigoCiudadOrigen' => $form->get('ciudadOrigen')->getData(),
                        'codigoCiudadDestino' => $form->get('ciudadDestino')->getData(),
                        'codigoDespacho' => $form->get('codigoDespacho')->getData()
                    ]
                ];
                $respuestaFormato2 = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosFormato2, $urlFormato2);
                if ($respuestaFormato2->error == false) {
                    $archivo = "/var/www/html/temporal/guia.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuestaFormato2->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="guia.pdf";');
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
            if ($form->get('btnFormato3')->isClicked()) {
                $urlFormato3 = "/transporte/api/oxigeno/guia/imprimir";
                $parametrosFormato3 = [
                    'formato' => 3,
                    'copias' => 1,
                    'filtros' => [
                        'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                        'codigoGuiaDesde' => $form->get('codigo')->getData(),
                        'codigoGuiaHasta' => $form->get('codigo')->getData(),
                        'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d'),
                        'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d'),
                        'codigoCiudadOrigen' => $form->get('ciudadOrigen')->getData(),
                        'codigoCiudadDestino' => $form->get('ciudadDestino')->getData(),
                        'codigoDespacho' => $form->get('codigoDespacho')->getData()
                    ]
                ];
                $respuestaFormato3 = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosFormato3, $urlFormato3);
                if ($respuestaFormato3) {
                    if ($respuestaFormato3->error == false) {
                        $archivo = "/var/www/html/temporal/guia.pdf";
                        $file = fopen($archivo, "wb");
                        fwrite($file, base64_decode($respuestaFormato3->base64));
                        fclose($file);
                        $response = new Response();
                        $response->headers->set('Cache-Control', 'private');
                        $response->headers->set('Content-type', 'application/pdf');
                        $response->headers->set('Content-Disposition', 'attachment; filename="guia.pdf";');
                        $response->sendHeaders();
                        $response->setContent(readfile($archivo));
                        return $response;
                    }
                }
            }
            if ($form->get('btnFormato4')->isClicked()) {
                $urlFormato4 = "/transporte/api/oxigeno/guia/imprimir";
                $parametrosFormato4 = [
                    'formato' => 4,
                    'copias' => 1,
                    'filtros' => [
                        'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                        'codigoGuiaDesde' => $form->get('codigo')->getData(),
                        'codigoGuiaHasta' => $form->get('codigo')->getData(),
                        'fechaDesde' => $form->get('fechaDesde')->getData()->format('Y-m-d'),
                        'fechaHasta' => $form->get('fechaHasta')->getData()->format('Y-m-d'),
                        'codigoCiudadOrigen' => $form->get('ciudadOrigen')->getData(),
                        'codigoCiudadDestino' => $form->get('ciudadDestino')->getData(),
                        'codigoDespacho' => $form->get('codigoDespacho')->getData()
                    ]
                ];
                $respuestaFormato4 = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosFormato4, $urlFormato4);
                if ($respuestaFormato4) {
                    if ($respuestaFormato4->error == false) {
                        $archivo = "/var/www/html/temporal/guia.pdf";
                        $file = fopen($archivo, "wb");
                        fwrite($file, base64_decode($respuestaFormato4->base64));
                        fclose($file);
                        $response = new Response();
                        $response->headers->set('Cache-Control', 'private');
                        $response->headers->set('Content-type', 'application/pdf');
                        $response->headers->set('Content-Disposition', 'attachment; filename="guia.pdf";');
                        $response->sendHeaders();
                        $response->setContent(readfile($archivo));
                        return $response;
                    }
                }
            }
            if ($form->get('btnPdf')->isClicked()) {
                $formato = new Guias();
                $formato->Generar($em, $arUsuario, $parametros);
            }
            if ($form->get('btnPdf2')->isClicked()) {
                $formato = new Guias2();
                $formato->Generar($em, $arUsuario, $parametros);
            }
            if ($form->get('btnExcel')->isClicked()) {
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url, true);
                if ($respuesta['error'] == false) {
                    $arrGuias = $respuesta['guias'];
                    $this->excelLista($arrGuias);
                }
            }
        }
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if ($respuesta != null) {
            if ($respuesta->error == false) {
                $arrGuias = $respuesta->guias;
            }
        }
        return $this->render('aplicacion/cliente/transporte/guia/lista.html.twig', [
            'arGuias' => $arrGuias,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente/transporte/guia/nuevo/{id}", name="cliente_transporte_guia_nuevo")
     */
    public function nuevo(Request $request, $id)
    {
        $arUsuario = $this->getUser();
        $arrDatos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(),
            [
                'codigoOperacion' => $arUsuario->getCodigoOperacionFk(),
                'codigoTercero' => $arUsuario->getCodigoTerceroErpFk()
            ],
            "/transporte/api/oxigeno/guia/nuevodatos", true);
        $arrDestinos = $this->fuenteChoiceCiudad($arrDatos['arrCiudad']);
        $arrGuiaTipo = $this->fuenteChoiceGuiaTipo($arrDatos['arrGuiaTipo'], $arrDatos['arrTercero']);
        $arrLiquidaciones = $this->fuenteChoiceLiquidacion($arrDatos['arrTercero']);
        $arrProductos = $this->fuenteChoiceProductos($arrDatos['arrProductos']);
        $arrFlete = array('required' => true, 'data' => 0, 'attr' => ['readonly' => true]);
        $arrManejo = array('required' => true, 'data' => 0, 'attr' => ['readonly' => true]);
        $arrRecaudo = array('required' => true, 'data' => 0, 'attr' => ['readonly' => false]);
        if ($arUsuario->isCambiarValoresGuia()) {
            $arrFlete['attr']['readonly'] = false;
            $arrManejo['attr']['readonly'] = false;
        }
        if ($arUsuario->isBloquearRecaudo()) {
            $arrRecaudo['attr']['readonly'] = true;
        }
        $form = $this->createFormBuilder()
            ->add('liquidacionRel', ChoiceType::class, array('choices' => $arrLiquidaciones, 'required' => true, 'attr' => ['class' => 'aplicarSelect2']))
            ->add('guiaTipoRel', ChoiceType::class, array('choices' => $arrGuiaTipo, 'required' => true, 'attr' => ['class' => 'aplicarSelect2']))
            ->add('productoRel', ChoiceType::class, array('choices' => $arrProductos, 'attr' => ['class' => 'aplicarSelect2']))
            ->add('destinoRel', ChoiceType::class, array('choices' => $arrDestinos, 'attr' => ['class' => 'aplicarSelect2']))
            ->add('documentoCliente', TextType::class, array('required' => false))
            ->add('remitente', TextType::class, array('required' => false))
            ->add('identificacionTipo', ChoiceType::class, array('choices' => array('CC' => 'CC', 'NI' => 'NI')))
            ->add('identificacion', TextType::class, array('required' => true, 'attr' => ['placeholder' => 'Identificación']))
            ->add('codigoAdquiriente', TextType::class, array('required' => true, 'data' => $arrDatos['arrTercero']['codigoTerceroPk'], 'attr' => ['placeholder' => 'Identificación adquiriente', 'readonly'=>'readonly' ]))
            ->add('tipoIdentificacionAdquiriente', TextType::class, array('required' => true, 'data' => $arrDatos['arrTercero']['codigoIdentificacionFk'], 'attr' => ['placeholder' => 'Identificación adquiriente', 'readonly'=>'readonly' ]))
            ->add('identificacionAdquiriente', TextType::class, array('required' => true, 'data' => $arrDatos['arrTercero']['numeroIdentificacion'], 'attr' => ['placeholder' => 'Identificación adquiriente', 'readonly'=>'readonly']))
            ->add('nombreAdquiriente', TextType::class, array('required' => true, 'data' => $arrDatos['arrTercero']['nombreCorto'], 'attr' => ['placeholder' => 'Nombre adquiriente', 'readonly'=>'readonly']))
            ->add('destinatario', TextType::class, array('required' => true, 'attr' => ['placeholder' => 'Destinatario']))
            ->add('direccion', TextType::class, array('required' => true))
            ->add('telefono', TextType::class, array('required' => true))
            ->add('unidades', NumberType::class, array('required' => true, 'data' => 1))
            ->add('pesoReal', NumberType::class, array('required' => true, 'data' => 0))
            ->add('pesoVolumen', NumberType::class, array('required' => true, 'data' => 0))
            ->add('pesoFacturado', NumberType::class, array('required' => true, 'data' => 0))
            ->add('declarado', NumberType::class, array('required' => true, 'data' => 0))
            ->add('flete', NumberType::class, $arrFlete)
            ->add('manejo', NumberType::class, $arrManejo)
            ->add('total', NumberType::class, array('required' => true, 'data' => 0, 'attr' => ['readonly' => true]))
            ->add('vrPeso', NumberType::class, array('required' => true, 'data' => 0, 'attr' => ['readonly' => true]))
            ->add('vrUnidad', NumberType::class, array('required' => true, 'data' => 0, 'attr' => ['readonly' => true]))
            ->add('recaudo', NumberType::class, $arrRecaudo)
            ->add('devolverDocumentoCliente', CheckboxType::class, array('required' => false))
            ->add('comentario', TextareaType::class, ['required' => false, 'attr' => ['rows' => '20', 'style' => 'height:100px']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $guiaTipo = $form->get('guiaTipoRel')->getData();
                if ($guiaTipo) {
                    $liquidacion = $form->get('liquidacionRel')->getData();
                    if ($liquidacion) {
                        if (is_numeric($form->get('flete')->getData()) && is_numeric($form->get('manejo')->getData())) {
                            if ($form->get('unidades')->getData() > 0 && $form->get('pesoReal')->getData() > 0 && $form->get('pesoFacturado')->getData() > 0) {
                                $validarFlete = $this->validarFlete($arrDatos['arrGuiaTipo'], $form->get('guiaTipoRel')->getData());
                                if (!$validarFlete || $validarFlete && $form->get('flete')->getData() > 0) {
                                    $estadoRecogido = $arUsuario->isEstadoRecogido();
                                    $estadoIngreso = $arUsuario->isEstadoIngreso();
                                    $codigoAdquiriente = $arUsuario->getCodigoTerceroErpFk();
                                    if($arUsuario->isBloquearAdquirienteCredito()) {
                                        $arrTipoGuias = $arrDatos['arrGuiaTipo'];
                                        $tipoDestino = false;
                                        foreach ($arrTipoGuias as $arrTipoGuia) {
                                            if($arrTipoGuia['codigoGuiaTipoPk'] == $guiaTipo) {
                                                if($arrTipoGuia['destino'] == true) {
                                                    $tipoDestino = true;
                                                }
                                            }
                                        }
                                        if($tipoDestino) {
                                            $codigoAdquiriente = $form->get('codigoAdquiriente')->getData();
                                        }
                                    }
                                    $parametros = [
                                        'codigoGuiaTipoFk' => $form->get('guiaTipoRel')->getData(),
                                        'codigoTerceroFk' => $arUsuario->getCodigoTerceroErpFk(),
                                        'codigoAdquirienteFk' => $codigoAdquiriente,
                                        'codigoOperacionFk' => $arrDatos['arrOperacion']['codigoOperacionPk'],
                                        'codigoCiudadOrigenFk' => $arrDatos['arrOperacion']['codigoCiudadFk'],
                                        'documentoCliente' => $form->get('documentoCliente')->getData(),
                                        'remitente' => $form->get('remitente')->getData(),
                                        'identificacionTipoDestinatario' => $form->get('identificacionTipo')->getData(),
                                        'identificacionDestinatario' => $form->get('identificacion')->getData(),
                                        'nombreDestinatario' => $form->get('destinatario')->getData(),
                                        'direccionDestinatario' => $form->get('direccion')->getData(),
                                        'telefonoDestinatario' => $form->get('telefono')->getData(),
                                        'codigoCiudadDestinoFk' => $form->get('destinoRel')->getData(),
                                        'codigoProductoFk' => $form->get('productoRel')->getData(),
                                        'unidades' => $form->get('unidades')->getData(),
                                        'pesoReal' => $form->get('pesoReal')->getData(),
                                        'pesoVolumen' => $form->get('pesoVolumen')->getData(),
                                        'pesoFacturado' => $form->get('pesoFacturado')->getData(),
                                        'vrDeclarado' => $form->get('declarado')->getData(),
                                        'vrFlete' => $form->get('flete')->getData(),
                                        'vrManejo' => $form->get('manejo')->getData(),
                                        'vrRecaudo' => $form->get('recaudo')->getData(),
                                        'comentario' => $form->get('comentario')->getData(),
                                        'usuario' => $this->getUser()->getUsername(),
                                        'tipoLiquidacion' => $form->get('liquidacionRel')->getData(),
                                        'codigoTerceroOperacionFk' => $this->getUser()->getCodigoOperacionClienteFk(),
                                        'estadoRecogido' => $estadoRecogido,
                                        'estadoIngreso' => $estadoIngreso,
                                        'devolverDocumentoCliente' => $form->get('devolverDocumentoCliente')->getData()
                                    ];
                                    $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/guia/nuevo");
                                    if ($respuesta->error == false) {
                                        return $this->redirect($this->generateUrl('cliente_transporte_guia_nuevo_respuesta', ['codigoGuia' => $respuesta->codigoGuiaPk]));
                                    } else {
                                        Mensajes::error($respuesta->errorMensaje);
                                    }
                                } else {
                                    Mensajes::error("La guia debe tener un flete");
                                }
                            } else {
                                Mensajes::error("Debe especificar las unidades, peso real y peso facturado");
                            }
                        } else {
                            Mensajes::error("El flete o manejo no son validos");
                        }
                    } else {
                        Mensajes::error("No se ha especificado un tipo de liquidacion");
                    }
                } else {
                    Mensajes::error("No se ha especificado un tipo de guia");
                }

            }
        }
        return $this->render('aplicacion/cliente/transporte/guia/nuevo.html.twig', [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
            'codigoPrecio' => $arrDatos['arrTercero']['codigoPrecioFk'],
            'codigoOrigen' => $arrDatos['arrOperacion']['codigoCiudadFk'],
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente/transporte/guia/detalle/{id}", name="cliente_transporte_guia_detalle")
     */
    public function detalle(Request $request, $id)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }
        $parametros = ['codigoGuia' => $id];
        $arrGuia = [];
        $arrDespachos = [];
        $arrNovedades = [];
        $arrFirmas = null;
        $arrSeguimientos = [];
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/guia/detalle");
        if ($respuesta->error == false) {
            $arrGuia = $respuesta->guia;
            $arrArchivos = $respuesta->archivos;
            $arrDespachos = $respuesta->despachos;
            $arrNovedades = $respuesta->novedades;
            if ($respuesta->firmas !== null) {
                $arrFirmas = $respuesta->firmas;
            }
            if ($respuesta->seguimientoFirmas !== null) {
                foreach ($respuesta->seguimientoFirmas as $seguimiento) {
                    $data = json_decode($seguimiento->datos);
                    $arrData = (array)$data;
                    array_push($arrSeguimientos, [
                        'cabeceras' => array_keys($arrData),
                        'contenido' => array_values($arrData)
                    ]);
                }
            }
        }
        return $this->render('aplicacion/cliente/transporte/guia/detalle.html.twig', [
            'arGuia' => $arrGuia,
            'arrArchivos' => $arrArchivos,
            'arrDespachos' => $arrDespachos,
            'arrNovedades' => $arrNovedades,
            'arrFirmas' => $arrFirmas,
            'arrSeguimientos' => $arrSeguimientos,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente/transporte/guia/buscardestinatario", name="cliente_transporte_guia_buscardestinatario")
     */
    public function buscarDestinatario(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $url = "/transporte/api/oxigeno/destinatario/lista";
        $arrGuias = [];
        $arrDatos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(),
            [
                'codigoDestinatarioPk' => 0,
                'codigoTercero' => $arUsuario->getCodigoTerceroErpFk()
            ],
            "/transporte/api/oxigeno/destinatario/nuevodatos", true);
        $arrDestinatario = $arrDatos['arrDestinatario'];
        $arrCiudades = $this->fuenteChoiceCiudad($arrDatos['arrCiudad']);
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
        ];
        $form = $this->createFormBuilder()
            ->add('nombre', TextType::class, array('required' => false))
            ->add('numeroIdentificacionLista', TextType::class, array('required' => false))
            ->add('codigoDestinatario', TextType::class, array('required' => false))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnFiltro')->isClicked()) {
                $parametros['nombreCorto'] = $form->get('nombre')->getData();
                $parametros['numeroIdentificacion'] = $form->get('numeroIdentificacionLista')->getData();
                $parametros['codigoDestinatario'] = $form->get('codigoDestinatario')->getData();
            }
        }

        $formNuevo = $this->createFormBuilder()
            ->add('numeroIdentificacion', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['numeroIdentificacion'] : ''))
            ->add('nombreCorto', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['nombreCorto'] : ''))
            ->add('direccion', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['direccion'] : ''))
            ->add('telefono', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['telefono'] : ''))
            ->add('ciudadRel', ChoiceType::class, ['choices' => $arrCiudades, 'data' => $arrDestinatario ? $arrDestinatario['codigoCiudadFk'] : ''])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $formNuevo->handleRequest($request);
        if ($formNuevo->isSubmitted() && $formNuevo->isValid()) {
            if ($formNuevo->get('btnGuardar')->isClicked()) {
                $parametrosDestinatario = [
                    'codigoTerceroFk' => $arUsuario->getCodigoTerceroErpFk(),
                    'numeroIdentificacion' => $formNuevo->get('numeroIdentificacion')->getData(),
                    'nombreCorto' => $formNuevo->get('nombreCorto')->getData(),
                    'direccion' => $formNuevo->get('direccion')->getData(),
                    'telefono' => $formNuevo->get('telefono')->getData(),
                    'codigoCiudadFk' => $formNuevo->get('ciudadRel')->getData(),
                ];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosDestinatario, "/transporte/api/oxigeno/destinatario/nuevo");
                if ($respuesta->error == false) {
                    Mensajes::success("Destinatario creado");
                    return $this->redirect($this->generateUrl('cliente_transporte_guia_buscardestinatario'));
                }
            }
        }

        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        $arrDestinatarios = [];
        if ($respuesta->error == false) {
            $arrDestinatarios = $respuesta->destinatarios;
        }
        $arrDestinatarios = $paginator->paginate($arrDestinatarios, $request->query->getInt('page', 1), 15);


        return $this->render('aplicacion/cliente/transporte/guia/buscarDestinatario.html.twig', [
            'arDestinatarios' => $arrDestinatarios,
            'formNuevo' => $formNuevo->createView(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cliente/transporte/guia/nuevo/respuesta/{codigoGuia}", name="cliente_transporte_guia_nuevo_respuesta")
     */
    public function nuevoRespuesta(Request $request, $codigoGuia)
    {
        $arUsuario = $this->getUser();
        $arrBotonRotulo1 = array('label' => 'Rotulo 1', 'disabled' => false);

        $form = $this->createFormBuilder()
            ->add('btnRotulo1', SubmitType::class, $arrBotonRotulo1)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->get('btnRotulo1')->isClicked()) {
                $urlRotulo = "/transporte/api/oxigeno/guia/rotulo";
                $parametrosRotulo = [
                    'formato' => 1,
                    'filtros' => [
                        'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                        'codigoGuiaDesde' => $codigoGuia,
                        'codigoGuiaHasta' => $codigoGuia,
                        'fechaDesde' => null,
                        'fechaHasta' => null,
                        'codigoCiudadOrigen' => null,
                        'codigoCiudadDestino' => null,
                        'codigoDespacho' => null
                    ]
                ];
                $respuestaRotulo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosRotulo, $urlRotulo);
                if ($respuestaRotulo->error == false) {
                    $archivo = "/var/www/html/temporal/rotulo.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuestaRotulo->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="rotulo.pdf";');
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
        }

        return $this->render('aplicacion/cliente/transporte/guiaNuevoRespuesta.html.twig', [
            'codigoGuia' => $codigoGuia,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente/buscar/ciudad/{campoCodigo}/{campoNombre}", name="cliente_transporte_guia_buscar_ciudad")
     */
    public function buscarCiudadOrigen(Request $request, PaginatorInterface $paginator, $campoCodigo, $campoNombre)
    {
        $arUsuario = $this->getUser();
        $parametros = [];
        $form = $this->createFormBuilder()
            ->add('codigo', TextType::class, array('required' => false))
            ->add('nombre', TextType::class, array('required' => false))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnFiltro')->isClicked()) {
                $parametros['nombre'] = $form->get('nombre')->getData();
                $parametros['codigoCiudad'] = $form->get('codigo')->getData();
            }
        }
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/ciudad/lista");
        $arrCiudades = [];
        if ($respuesta !== null) {
            if ($respuesta->error == false) {
                $arrCiudades = $respuesta->arrCiudad;
            }
        }

        $arrCiudades = $paginator->paginate($arrCiudades, $request->query->getInt('page', 1), 100);
        return $this->render('aplicacion/cliente/transporte/guia/buscarCiudad.html.twig', [
            'arrCiudades' => $arrCiudades,
            'form' => $form->createView(),
            'campoCodigo' => $campoCodigo,
            'campoNombre' => $campoNombre
        ]);
    }


    /**
     * @Route("/cliente/transporte/guia/buscaradquiriente", name="cliente_transporte_guia_buscaradquiriente")
     */
    public function buscarTercero(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arrDatos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(),
            [
                'codigoTercero' => $arUsuario->getCodigoTerceroErpFk()
            ],
            "/transporte/api/oxigeno/tercero/nuevodatos", true);
        $arrIdentificaciones = $this->fuenteChoiceIdenticaciones($arrDatos['arrIdentificaciones']);
        $arrCiudades = $this->fuenteChoiceCiudad($arrDatos['arrCiudades']);
        $parametrosLista = [];
        $form = $this->createFormBuilder()
            ->add('nombre', TextType::class, array('required' => false))
            ->add('numeroIdentificacionLista', TextType::class, array('required' => false))
            ->add('codigoTercero', IntegerType::class, array('required' => false))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnFiltro')->isClicked()) {
                $parametrosLista['nombre'] = $form->get('nombre')->getData();
                $parametrosLista['numeroIdentificacion'] = $form->get('numeroIdentificacionLista')->getData();
                $parametrosLista['codigoTercero'] = $form->get('codigoTercero')->getData();
            }
        }
        $formNuevo = $this->createFormBuilder()
            ->add('numeroIdentificacion', TextType::class, array('required' => true))
            ->add('nombreCorto', TextType::class, array('required' => true))
            ->add('telefono', TextType::class, array('required' => true))
            ->add('celular', TextType::class, array('required' => true))
            ->add('direccion', TextType::class, array('required' => true))
            ->add('nombre1', TextType::class, ['required' => true, 'label' => 'Primer nombre:'])
            ->add('nombre2', TextType::class, ['required' => false, 'label' => 'Segundo nombre:'])
            ->add('apellido1', TextType::class, ['required' => true, 'label' => 'Primer apellido:'])
            ->add('apellido2', TextType::class, ['required' => false, 'label' => 'Segundo apellido:'])
            ->add('correo', EmailType::class, ['required' => true, 'label' => 'Correo:'])
            ->add('codigoIdentificacionFk', ChoiceType::class, ['choices' => $arrIdentificaciones, 'attr' => ['class' => 'aplicarSelect2']])
            ->add('codigoCiudadFk', ChoiceType::class, ['choices' => $arrCiudades, 'attr' => ['class' => 'aplicarSelect2']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $formNuevo->handleRequest($request);
        if ($formNuevo->isSubmitted() && $formNuevo->isValid()) {
            if ($formNuevo->get('btnGuardar')->isClicked()) {
                $error = false;
                $identificacion = $formNuevo->get('codigoIdentificacionFk')->getData();
                if($identificacion == 'NI') {
                    $numeroIdentificacion = $formNuevo->get('numeroIdentificacion')->getData();
                    $caracteresNumeroIdentificacion = strlen($numeroIdentificacion);
                    if($caracteresNumeroIdentificacion != 9) {
                        $error = true;
                        Mensajes::error("El numero de identificacion de NIT debe ser de 9 digitos");
                    }
                }
                if($error == false) {
                    $parametrosTercero = [
                        'codigoTerceroFk' => $arUsuario->getCodigoTerceroErpFk(),
                        'numeroIdentificacion' => $formNuevo->get('numeroIdentificacion')->getData(),
                        'nombreCorto' => $formNuevo->get('nombreCorto')->getData(),
                        'nombre1' => $formNuevo->get('nombre1')->getData(),
                        'nombre2' => $formNuevo->get('nombre2')->getData(),
                        'apellido1' => $formNuevo->get('apellido1')->getData(),
                        'apellido2' => $formNuevo->get('apellido2')->getData(),
                        'direccion' => $formNuevo->get('direccion')->getData(),
                        'telefono' => $formNuevo->get('telefono')->getData(),
                        'celular' => $formNuevo->get('celular')->getData(),
                        'correo' => $formNuevo->get('correo')->getData(),
                        'codigoCiudadFk' => $formNuevo->get('codigoCiudadFk')->getData(),
                        'codigoIdentificacionFk' => $identificacion,
                        'codigoOperacion' => $this->getUser()->getCodigoOperacionFk(),
                        'usuario' => $this->getUser()->getUserIdentifier()
                    ];

                    $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosTercero, "/transporte/api/oxigeno/tercero/nuevo");

                    if ($respuesta->error == false) {
                        echo "<script>
                        let numeroIdentificacion = '{$formNuevo->get('numeroIdentificacion')->getData()}'
                        let nombreCorto = '{$formNuevo->get('nombreCorto')->getData()}'
                        let IdentificacionFk = '{$formNuevo->get('codigoIdentificacionFk')->getData()}'
                        let codigoTercero = '{$respuesta->codigoTerceroPk}'

                        
                        window.opener.document.getElementById('form_identificacionAdquiriente').value = numeroIdentificacion;
                        window.opener.document.getElementById('form_nombreAdquiriente').value = nombreCorto;
                        window.opener.document.getElementById('form_tipoIdentificacionAdquiriente').value = IdentificacionFk;
                        window.opener.document.getElementById('form_codigoAdquiriente').value = codigoTercero;
                        window.close()
                    </script>";
                    } else {
                        Mensajes::error($respuesta->error);
                    }
                }
            }
        }
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosLista, "/transporte/api/oxigeno/tercero/lista");
        $arrTerceros = [];
        if ($respuesta->error == false) {
            $arrTerceros = $respuesta->terceros;
        }
        $arrTerceros = $paginator->paginate($arrTerceros, $request->query->getInt('page', 1), 15);


        return $this->render('aplicacion/cliente/transporte/guia/buscarAdquiriente.html.twig', [
            'arTerceros' => $arrTerceros,
            'formNuevo' => $formNuevo->createView(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cliente/transporte/guia/archivosmasivos/{codigoGuia}", name="cliente_transporte_guia_archivosmasivos")
     */
    public function archivosMasivosGuia(Request $request, PaginatorInterface $paginator, $codigoGuia)
    {
        $arUsuario = $this->getUser();
        $parametros = [
            'codigoIdentificador' => $codigoGuia,
            'tipoArchivo' => 'TteGuia'
        ];
        $arrMasivos = [];
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $codigoMasivo = $request->request->get('btnDescargar');
            $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [
                'codigoArchivoMasivo' => $codigoMasivo
            ], "/transporte/api/oxigeno/archivomasivos/detalle");
            if ($respuesta->error == false) {
                $fileContent = base64_decode($respuesta->base64);
                header('Content-Type: ' . $respuesta->tipo);
                header('Content-Disposition: attachment; filename="' . $respuesta->archivo . '"');
                echo $fileContent;
            } else {
                Mensajes::error(utf8_decode($respuesta->errorMensaje));
            }
        }


        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/archivosmasivos/lista");
        if ($respuesta->error == false) {
            $arrMasivos = $respuesta->arrMasivos;
        }
        return $this->render('aplicacion/cliente/transporte/guia/archivosMasivos.html.twig', [
            'arrMasivos' => $arrMasivos,
            'form' => $form->createView()
        ]);
    }

    private function fuenteChoiceIdenticaciones($datos)
    {
        $arrDatos = ['' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato['nombre']} "] = $dato['codigoIdentificacionPk'];
        }
        return $arrDatos;
    }

    private function fuenteChoiceCiudad($datos)
    {
        $arrDatos = ['' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato['nombre']} - {$dato['departamentoNombre']} [{$dato['codigoCiudadPk']}]"] = $dato['codigoCiudadPk'];
        }
        return $arrDatos;
    }

    private function fuenteChoiceGuiaTipo($datos, $tercero)
    {
        $arrDatos = [];
        foreach ($datos as $dato) {
            $validacion = true;
            if ($dato['contado'] && $tercero['guiaPagoContado'] == false) {
                $validacion = false;
            }
            if ($dato['destino'] && $tercero['guiaPagoDestino'] == false) {
                $validacion = false;
            }
            if ($dato['corriente'] && $tercero['guiaPagoCredito'] == false) {
                $validacion = false;
            }
            if ($dato['cortesia'] && $tercero['guiaPagoCortesia'] == false) {
                $validacion = false;
            }
            if ($validacion) {
                $arrDatos["{$dato['nombre']}"] = $dato['codigoGuiaTipoPk'];
            }
        }
        return $arrDatos;
    }

    private function fuenteChoiceLiquidacion($tercero)
    {
        $arrDatos = [];
        if ($tercero['liquidarGuiaPeso']) {
            $arrDatos['KILO'] = 'K';
        }
        if ($tercero['liquidarGuiaUnidad']) {
            $arrDatos['UNIDAD'] = 'U';
        }
        return $arrDatos;
    }

    private function fuenteChoiceProductos($datos)
    {
        $arrDatos = ['VARIOS' => 'VARIOS'];
        foreach ($datos as $dato) {
            if ($dato['codigoProductoFk'] != 'VARIOS') {
                $arrDatos["{$dato['productoNombre']}"] = $dato['codigoProductoFk'];
            }
        }
        return $arrDatos;
    }

    private function fuenteChoiceGuiasTipos($datos)
    {
        $arrDatos = ['Todos' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato->nombre}"] = $dato->codigoGuiaTipoPk;
        }
        return $arrDatos;
    }

    private function fuenteChoiceCiudadOrigenDestio($datos)
    {
        $arrDatos = ['Todos' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato->nombre} - $dato->departamentoNombre [$dato->codigoCiudadPk]"] = $dato->codigoCiudadPk;
        }
        return $arrDatos;
    }

    private function validarFlete($arrGuiaTipo, $tipo)
    {
        $validarFlete = false;
        foreach ($arrGuiaTipo as $guiaTipo) {
            if ($guiaTipo['codigoGuiaTipoPk'] == $tipo) {
                if ($guiaTipo['validarFlete']) {
                    $validarFlete = true;
                }
                if ($guiaTipo['cortesia']) {
                    $validarFlete = false;
                }
            }
        }
        return $validarFlete;
    }

    public function excelLista($arRegistros)
    {
        set_time_limit(0);
        ini_set("memory_limit", -1);
        if ($arRegistros) {
            $libro = new Spreadsheet();
            $hoja = $libro->getActiveSheet();
            $hoja->getStyle(1)->getFont()->setName('Arial')->setSize(8);
            $hoja->setTitle('Items');
            $j = 0;
            $arrColumnas = ['GUIA', 'TIPO', 'FECHA', 'DOCUMENTO', 'ORIGEN', 'DESTINO', 'DESTINATARIO', 'DIRECCION', 'TELEFONO', 'COMENTARIO', 'UND', 'PESO', 'VOL', 'RECAUDO', 'FLETE', 'DECLARADO', 'MANEJO', 'TOTAL',
                'REC', 'FH_REC', 'SCL', 'DES', 'FH_DES', 'ENT', 'FH_ENT', 'CUM', 'FAC', 'NOV', 'DIG', 'OPC'];
            for ($i = 'A'; $j <= sizeof($arrColumnas) - 1; $i++) {
                $hoja->getColumnDimension($i)->setAutoSize(true);
                $hoja->getStyle(1)->getFont()->setName('Arial')->setSize(8);
                $hoja->getStyle(1)->getFont()->setBold(true);
                $hoja->setCellValue($i . '1', strtoupper($arrColumnas[$j]));
                $j++;
            }
            $j = 2;
            foreach ($arRegistros as $arRegistro) {
                $hoja->getStyle($j)->getFont()->setName('Arial')->setSize(8);
                $hoja->setCellValue('A' . $j, $arRegistro['codigoGuiaPk']);
                $hoja->setCellValue('B' . $j, $arRegistro['codigoGuiaTipoFk']);
                $hoja->setCellValue('C' . $j, $arRegistro['fechaIngreso']);
                $hoja->setCellValue('D' . $j, $arRegistro['documentoCliente']);
                $hoja->setCellValue('E' . $j, $arRegistro['ciudadOrigen']);
                $hoja->setCellValue('F' . $j, $arRegistro['ciudadDestino']);
                $hoja->setCellValue('G' . $j, $arRegistro['nombreDestinatario']);
                $hoja->setCellValue('H' . $j, $arRegistro['direccionDestinatario']);
                $hoja->setCellValue('I' . $j, $arRegistro['telefonoDestinatario']);
                $hoja->setCellValue('J' . $j, $arRegistro['comentario']);
                $hoja->setCellValue('K' . $j, $arRegistro['unidades']);
                $hoja->setCellValue('L' . $j, $arRegistro['pesoReal']);
                $hoja->setCellValue('M' . $j, $arRegistro['pesoVolumen']);
                $hoja->setCellValue('N' . $j, $arRegistro['vrRecaudo']);
                $hoja->setCellValue('O' . $j, $arRegistro['vrFlete']);
                $hoja->setCellValue('P' . $j, $arRegistro['vrDeclara']);
                $hoja->setCellValue('Q' . $j, $arRegistro['vrManejo']);
                $hoja->setCellValue('R' . $j, $arRegistro['vrFlete'] + $arRegistro['vrManejo']);
                $hoja->setCellValue('S' . $j, $arRegistro['estadoRecogido'] ? 'SI' : 'NO');
                $hoja->setCellValue('T' . $j, $arRegistro['fechaRecogido'] ? date_create($arRegistro['fechaRecogido'])->format('Y-m-d') : '');
                $hoja->setCellValue('U' . $j, $arRegistro['estadoSalidaCliente'] ? 'SI' : 'NO');
                $hoja->setCellValue('V' . $j, $arRegistro['estadoDespachado'] ? 'SI' : 'NO');
                $hoja->setCellValue('W' . $j, $arRegistro['fechaDespacho'] ? date_create($arRegistro['fechaDespacho'])->format('Y-m-d') : '');
                $hoja->setCellValue('X' . $j, $arRegistro['estadoEntregado'] ? 'SI' : 'NO');
                $hoja->setCellValue('Y' . $j, $arRegistro['fechaEntrega'] ? date_create($arRegistro['fechaEntrega'])->format('Y-m-d') : '');
                $hoja->setCellValue('Z' . $j, $arRegistro['estadoCumplido'] ? 'SI' : 'NO');
                $hoja->setCellValue('AA' . $j, $arRegistro['estadoFacturado'] ? 'SI' : 'NO');
                $hoja->setCellValue('AB' . $j, $arRegistro['estadoNovedad'] ? 'SI' : 'NO');
                $hoja->setCellValue('AC' . $j, $arRegistro['estadoDigitalizado'] ? 'SI' : 'NO');
                $hoja->setCellValue('AD' . $j, $arRegistro['terceroOperacionCodigo']);
                $j++;
            }
            $libro->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=guias.xls");
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($libro, 'Xls');
            $writer->save('php://output');
        } else {
            Mensajes::error("No existen registros para exportar");
        }
    }
}