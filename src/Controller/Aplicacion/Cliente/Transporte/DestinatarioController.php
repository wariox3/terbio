<?php


namespace App\Controller\Aplicacion\Cliente\Transporte;

use App\Controller\FuncionesController;
use App\Entity\Directorio;
use App\Entity\Empresa;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DestinatarioController extends AbstractController
{
    #[Route("/cliente/transporte/destinatario/lista", name:"cliente_transporte_destinatario_lista")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $url = "/transporte/api/oxigeno/destinatario/lista";
        $arrDestinatarios = [];
        $form = $this->createFormBuilder()
            ->add('nombre', TextType::class, array('required' => false))
            ->add('btnExcel', SubmitType::class, array('label' => 'Excel'))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
        ];
        if ($form->isSubmitted()) {
            if ($form->get('btnFiltro')->isClicked() || $form->get('btnExcel')->isClicked()) {
                $parametros['nombreCorto'] = $form->get('nombre')->getData();
            }

            if ($form->get('btnExcel')->isClicked()) {
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url, true);
                if ($respuesta['error'] == false) {
                    $arrDestinatarios = $respuesta['destinatarios'];
                    $this->excel($arrDestinatarios);
                }
            }
        }
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        $arrDestinatarios = [];
        if($respuesta !== null){
            if ($respuesta->error == false) {
                $arrDestinatarios = $respuesta->destinatarios;
            }
        }
        $arrDestinatarios = $paginator->paginate($arrDestinatarios, $request->query->getInt('page', 1), 100);

        return $this->render('aplicacion/cliente/transporte/destinatario/lista.html.twig', [
            'arDestinatarios' => $arrDestinatarios,
            'form' => $form->createView()
        ]);
    }

    #[Route("/cliente/transporte/destinatario/nuevo/{id}", name:"cliente_transporte_destinatario_nuevo")]
    public function nuevo(Request $request, $id)
    {
        $arUsuario = $this->getUser();
        $arrDatos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(),
            [
                'codigoDestinatarioPk' => $id,
                'codigoTercero' => $arUsuario->getCodigoTerceroErpFk()
            ],
            "/transporte/api/oxigeno/destinatario/nuevodatos", true);
        $arrDestinatario = $arrDatos['arrDestinatario'];
        $arrCiudades = $this->fuenteChoiceCiudad($arrDatos['arrCiudad']);
        $form = $this->createFormBuilder()
            ->add('numeroIdentificacion', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['numeroIdentificacion'] : ''))
            ->add('nombreCorto', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['nombreCorto'] : ''))
            ->add('direccion', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['direccion'] : ''))
            ->add('telefono', TextType::class, array('required' => true, 'data' => $arrDestinatario ? $arrDestinatario['telefono'] : ''))
            ->add('ciudadRel', ChoiceType::class, ['choices' => $arrCiudades, 'attr' => ['class' => 'aplicarSelect2'], 'data' => $arrDestinatario ? $arrDestinatario['codigoCiudadFk'] : ''])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametros = [
                    'codigoDestinatarioPk' => $id,
                    'codigoTerceroFk' => $arUsuario->getCodigoTerceroErpFk(),
                    'numeroIdentificacion' => $form->get('numeroIdentificacion')->getData(),
                    'nombreCorto' => $form->get('nombreCorto')->getData(),
                    'direccion' => $form->get('direccion')->getData(),
                    'telefono' => $form->get('telefono')->getData(),
                    'codigoCiudadFk' => $form->get('ciudadRel')->getData(),
                ];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/destinatario/nuevo");
                if ($respuesta->error == false) {
                    return $this->redirect($this->generateUrl('cliente_transporte_destinatario_lista'));
                }
            }
        }
        return $this->render('aplicacion/cliente/transporte/destinatario/nuevo.html.twig', [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
            'form' => $form->createView()
        ]);
    }

    #[Route("/cliente/transporte/destinatario/importarmasivo", name:"cliente_transporte_destinatario_importarMasivo")]
    public function importarMasivo(Request $request,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arrInconsistencias = [];
        $form = $this->createFormBuilder()
            ->add('attachment', FileType::class)
            ->add('BtnCargar', SubmitType::class, array('label' => 'Cargar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            set_time_limit(0);
            $objArchivo = $form['attachment']->getData();
            $directorioDestino = "/var/www/html/temporal/";
            if ($objArchivo->getSize()) {
                $form['attachment']->getData()->move($directorioDestino, "archivoImportarDestinatario.xls");
                $ruta = $directorioDestino . "archivoImportarDestinatario.xls";
                $objPHPExcel = IOFactory::load($ruta);
                $arrCargas = [];
                $i = 0;
                if ($objPHPExcel->getSheetCount() == 1) {
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        $highestRow = $worksheet->getHighestRow(); // e.g. 10
                        for ($row = 2; $row <= $highestRow; ++$row) {
                            $identificacion = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                            $numeroIdentificacion = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                            $nombreCorto = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                            $direccion = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                            $telefono = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                            $codigoCiudad = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                            $coreo = $worksheet->getCellByColumnAndRow(7, $row)->getValue() ?? null;
                            if ($identificacion && $numeroIdentificacion && $nombreCorto && $direccion && $telefono && $codigoCiudad) {
                                if ($identificacion == 'CC' || $identificacion == 'NI') {
                                    $parametros = [
                                        'codigoCiudad' => $codigoCiudad,
                                    ];
                                    $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/api/transporte/ciudad/validar");
                                    if ($respuesta) {
                                        if ($respuesta->error == false) {
                                            if ($respuesta->validacion == true) {
                                                $codigoCiudad = $respuesta->codigoCiudad;
                                                $parametros = [
                                                    'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                                                    'identificacion' => $identificacion,
                                                    'numeroIdentificacion' => $numeroIdentificacion
                                                ];
                                                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/api/transporte/destinatario/validar");
                                                if ($respuesta) {
                                                    if ($respuesta->error == false) {
                                                        if ($respuesta->validacion == false) {
                                                            $arrCargas [] = [
                                                                'identificacion' => $identificacion,
                                                                'numeroIdentificacion' => $numeroIdentificacion,
                                                                'nombreCorto' => $nombreCorto,
                                                                'direccion' => $direccion,
                                                                'telefono' => $telefono,
                                                                'codigoCiudadFk' => $codigoCiudad,
                                                                'correo' => $coreo
                                                            ];
                                                        } else {
                                                            $arrInconsistencias[] = [
                                                                'fila' => $i,
                                                                'inconsistencia' => "El destinatario ya existe"];
                                                        }
                                                    }
                                                }
                                            } else {
                                                $arrInconsistencias[] = [
                                                    'fila' => $i,
                                                    'inconsistencia' => "El codigo de la ciudad no existe"];
                                            }
                                        }
                                    }
                                } else {
                                    $arrInconsistencias[] = [
                                        'fila' => $i,
                                        'inconsistencia' => "Solo se permiten identificaciones CC o NI"];
                                }
                            } else {
                                $arrInconsistencias[] = [
                                    'fila' => $i,
                                    'inconsistencia' => "Todos los campos son requeridos"];
                            }
                            $i++;
                        }
                    }


                    foreach ($arrCargas as $arrCarga) {
                        $parametros = [
                            'codigoDestinatarioPk' => 0,
                            'codigoTerceroFk' => $arUsuario->getCodigoTerceroErpFk(),
                            'identificacion' => $arrCarga['identificacion'],
                            'numeroIdentificacion' => $arrCarga['numeroIdentificacion'],
                            'nombreCorto' => $arrCarga['nombreCorto'],
                            'direccion' => $arrCarga['direccion'],
                            'telefono' => $arrCarga['telefono'],
                            'codigoCiudadFk' => $arrCarga['codigoCiudadFk'],
                            'correo' => $arrCarga['correo']
                        ];
                        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/destinatario/nuevo");
                    }
                } else {
                    Mensajes::error('El documento debe contener solamente una hoja');
                }
            }
        }


        return $this->render('aplicacion/cliente/transporte/destinatario/importarMasivo.html.twig', [
            'form' => $form->createView(),
            'arrInconsistencias' => $arrInconsistencias
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
            $arrColumnas = ['ID', 'NIT', 'NOMBRE', 'DIRECCION', 'TELEFONO', 'CIUDAD'];
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
        } else {
            Mensajes::error("No existen registros para exportar");
        }
    }

    private function fuenteChoiceCiudad($datos)
    {
        $arrDatos = ['' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato['nombre']}"] = $dato['codigoCiudadPk'];
        }
        return $arrDatos;
    }
}