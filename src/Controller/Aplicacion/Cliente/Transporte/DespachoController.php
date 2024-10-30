<?php


namespace App\Controller\Aplicacion\Cliente\Transporte;

use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DespachoController  extends AbstractController
{
    #[Route("/cliente/transporte/despacho/lista", name:"cliente_transporte_despacho_lista")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arrDespachos = [];
        $form = $this->createFormBuilder()
            ->add('codigoVehiculo', TextType::class, array('required' => false))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        $parametros = [
            'codigoUsuario' => $arUsuario->getUsername(),
        ];
        if ($form->isSubmitted()) {
            if ($form->get('btnFiltro')->isClicked() || $form->get('btnExcel')->isClicked()) {
                $parametros['codigoVehiculo'] = $form->get('codigoVehiculo')->getData();
            }
        }
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despachos/lista");
        if($respuesta->error == false) {
            $arrDespachos = $respuesta->despachos;
        }
        $arrDespachos = $paginator->paginate($arrDespachos, $request->query->getInt('page', 1), 100);
        return $this->render('aplicacion/cliente/transporte/despacho/lista.html.twig',[
            'arDespachos' => $arrDespachos,
            'form'=>$form->createView()
        ]);
    }

    #[Route("/cliente/transporte/despacho/nuevo/{id}", name:"cliente_transporte_despacho_nuevo")]
    public function nuevo(Request $request, $id)
    {
        $arUsuario = $this->getUser();
        $parametros = [
            'codigoDespacho' => $id
        ];
        $arrDatos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/nuevodatos", true);
        $arrDespacho = $arrDatos['arrDespacho'];
        $arrCiudades = $this->fuenteChoiceCiudad($arrDatos['arrCiudad']);
        $arrDespachoTipo = $this->fuenteChoiceDespachoTipo($arrDatos['arrDespachoTipo']);
        $arrRuta = $this->fuenteChoiceRuta($arrDatos['arrRuta']);
        $form = $this->createFormBuilder()
            ->add('codigoConductorFk', TextType::class, array('required' => true, 'data' => $arrDespacho ? $arrDespacho['codigoConductorFk']:''))
            ->add('conductorNombreCorto', TextType::class, array('required' => false, 'data' => $arrDespacho ? $arrDespacho['conductorNombreCorto']:''))
            ->add('codigoVehiculoFk', TextType::class, array('required' => true, 'data' => $arrDespacho ? $arrDespacho['codigoVehiculoFk']:''))
            ->add('ciudadOrigenRel', ChoiceType::class, ['choices'  => $arrCiudades, 'data' => $arrDespacho ? $arrDespacho['codigoCiudadOrigenFk']:''])
            ->add('ciudadDestinoRel', ChoiceType::class, ['choices'  => $arrCiudades, 'data' => $arrDespacho ? $arrDespacho['codigoCiudadDestinoFk']:''])
            ->add('despachoTipoRel', ChoiceType::class, ['choices'  => $arrDespachoTipo, 'data' => $arrDespacho ? $arrDespacho['codigoDespachoTipoFk']:''])
            ->add('rutaRel', ChoiceType::class, ['choices'  => $arrRuta, 'data' => $arrDespacho ? $arrDespacho['codigoRutaFk']:''])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametros = [
                    'codigoDespachoPk' => $id,
                    'codigoDespachoTipo' => $form->get('despachoTipoRel')->getData(),
                    'codigoVehiculo' => strtoupper($form->get('codigoVehiculoFk')->getData()),
                    'codigoConductor' => $form->get('codigoConductorFk')->getData(),
                    'codigoOrigen' => $form->get('ciudadOrigenRel')->getData(),
                    'codigoDestino' => $form->get('ciudadDestinoRel')->getData(),
                    'codigoRuta' => $form->get('rutaRel')->getData(),
                    'codigoOperacion' => $arUsuario->getCodigoOperacionFk(),
                    'usuario' => $arUsuario->getUsername(),
                ];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/nuevo");
                if($respuesta->error == false) {
                    return $this->redirect($this->generateUrl('cliente_transporte_despacho_lista'));
                } else {
                    Mensajes::error($respuesta->errorMensaje);
                }
            }
        }
        return $this->render('aplicacion/cliente/transporte/despacho/nuevo.html.twig', [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
            'form' => $form->createView()
        ]);
    }

    #[Route("/cliente/transporte/despacho/detalle/{id}", name:"cliente_transporte_despacho_detalle")]
    public function detalle(Request $request, $id)
    {
        $arUsuario = $this->getUser();
        $parametros = ['codigoDespacho' => $id];
        $arrDespacho = [];
        $arrDetalles = [];
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/detalle");
        if($respuesta->error == false) {
            $arrDespacho = $respuesta->despacho;
            $arrDetalles = $respuesta->detalles;
        }
        $arrBotonAutorizar = array('label' => 'Autorizar', 'disabled' => false);
        $arrBotonAprobar = array('label' => 'Aprobar', 'disabled' => true);
        $arrBotonActualizar = array('label' => 'Actualizar', 'disabled' => false);
        $arrBotonImprimir = array('label' => 'Imprimir', 'disabled' => false);
        if ($arrDespacho->estadoAutorizado) {
            $arrBotonActualizar['disabled'] = true;
            $arrBotonAutorizar['disabled'] = true;
            if (!$arrDespacho->estadoAprobado) {
                $arrBotonAprobar['disabled'] = false;
            }
        }

        $form = $this->createFormBuilder()
            ->add('btnAutorizar', SubmitType::class, $arrBotonAutorizar)
            ->add('btnAprobar', SubmitType::class, $arrBotonAprobar)
            ->add('btnActualizar', SubmitType::class, $arrBotonActualizar)
            ->add('btnImprimir', SubmitType::class, $arrBotonImprimir)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnAutorizar')->isClicked()) {
                $parametros = ['codigoDespacho' => $id];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/autorizar");
                if($respuesta) {
                    if($respuesta->error) {
                        Mensajes::error($respuesta->errorMensaje);
                    }
                }
                return $this->redirect($this->generateUrl('cliente_transporte_despacho_detalle', array('id' => $id)));
            }
            if ($form->get('btnAprobar')->isClicked()) {
                $parametros = ['codigoDespacho' => $id];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/aprobar");
                if($respuesta) {
                    if($respuesta->error) {
                        Mensajes::error($respuesta->errorMensaje);
                    }
                }
                return $this->redirect($this->generateUrl('cliente_transporte_despacho_detalle', array('id' => $id)));
            }
            if ($form->get('btnActualizar')->isClicked()) {
                return $this->redirect($this->generateUrl('cliente_transporte_despacho_detalle', array('id' => $id)));
            }
            if ($form->get('btnImprimir')->isClicked()) {
                $parametros = [
                    'codigoDespacho' => $id
                ];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/imprimir");
                if($respuesta->error == false) {
                    $archivo = "/var/www/html/temporal/relacionDespacho{$id}.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuesta->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', "attachment; filename=relacionDespacho{$id}.pdf;");
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
        }
        return $this->render('aplicacion/cliente/transporte/despacho/detalle.html.twig', [
            'arDespacho' => $arrDespacho,
            'arDetalles' => $arrDetalles,
            'form' => $form->createView()
        ]);
    }

    #[Route("/cliente/transporte/despacho/buscarconductor", name:"cliente_transporte_despacho_buscarconductor")]
    public function buscarConductor(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $parametros = [];
        $form = $this->createFormBuilder()
            ->add('numeroIdentificacion', TextType::class, array('required' => false))
            ->add('nombre', TextType::class, array('required' => false))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnFiltro')->isClicked()) {
                $parametros['nombre'] = $form->get('nombre')->getData();
                $parametros['numeroIdentificacion'] = $form->get('numeroIdentificacion')->getData();
            }
        }
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/conductor/lista");
        $arrConductores = [];
        if($respuesta->error == false) {
            $arrConductores = $respuesta->conductores;
        }
        $arrConductores = $paginator->paginate($arrConductores, $request->query->getInt('page', 1), 100);


        return $this->render('aplicacion/cliente/transporte/despacho/buscarConductor.html.twig',[
            'arConductores' => $arrConductores,
            'form' => $form->createView(),
        ]);
    }

    public function excel($arRegistros)
    {
        set_time_limit(0);
        ini_set("memory_limit", -1);
        if ($arRegistros) {
            $libro = new Spreadsheet();
            $hoja = $libro->getActiveSheet();
            $hoja->getStyle(1)->getFont()->setName('Arial')->setSize(8);
            $hoja->setTitle('Items');
            $j = 0;
            $arrColumnas=['ID', 'NIT', 'NOMBRE', 'DIRECCION', 'TELEFONO', 'CIUDAD'];
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
                $hoja->setCellValue('A' . $j, $arRegistro['codigoDestinatarioPk']);
                $hoja->setCellValue('B' . $j, $arRegistro['numeroIdentificacion']);
                $hoja->setCellValue('C' . $j, $arRegistro['nombreCorto']);
                $hoja->setCellValue('D' . $j, $arRegistro['direccion']);
                $hoja->setCellValue('E' . $j, $arRegistro['telefono']);
                $hoja->setCellValue('F' . $j, $arRegistro['ciudadNombre']);
                $j++;
            }
            $libro->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=destinatarios.xls");
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($libro, 'Xls');
            $writer->save('php://output');
        }else{
            Mensajes::error("No existen registros para exportar");
        }
    }

    private function fuenteChoiceCiudad($datos) {
        $arrDatos = ['' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato['nombre']}"] = $dato['codigoCiudadPk'];
        }
        return $arrDatos;
    }

    private function fuenteChoiceDespachoTipo($datos) {
        $arrDatos = ['' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato['nombre']}"] = $dato['codigoDespachoTipoPk'];
        }
        return $arrDatos;
    }

    private function fuenteChoiceRuta($datos) {
        $arrDatos = ['' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato['nombre']}"] = $dato['codigoRutaPk'];
        }
        return $arrDatos;
    }


}