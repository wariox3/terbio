<?php


namespace App\Controller\Aplicacion\Cliente\Transporte;

use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NovedadController  extends AbstractController
{
    #[Route("/cliente/transporte/novedad/lista", name:"cliente_transporte_novedad_lista")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arrNovedades = [];
        $form = $this->createFormBuilder()
            ->add('codigoGuia', IntegerType::class, array('required' => false))
            ->add('documentoCliente', TextType::class, array('required' => false))
            ->add('estadoSolucionado', ChoiceType::class, ['choices' => ['TODOS' => '', 'SI' => '1', 'NO' => '0'], 'required' => false, 'data' => '0'])
            ->add('estadoAtendido', ChoiceType::class, ['choices' => ['TODOS' => '', 'SI' => '1', 'NO' => '0'], 'required' => false])
            ->add('btnExcel', SubmitType::class, array('label' => 'Excel'))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
            'estadoSolucionado' => 0
        ];
        if ($form->isSubmitted()) {
            if ($form->get('btnFiltro')->isClicked() || $form->get('btnExcel')->isClicked()) {
                $parametros['codigoGuia'] = $form->get('codigoGuia')->getData();
                $parametros['documentoCliente'] = $form->get('documentoCliente')->getData();
                $parametros['estadoSolucionado'] = $form->get('estadoSolucionado')->getData();
                $parametros['estadoAtendido'] = $form->get('estadoAtendido')->getData();
            }

            if ($form->get('btnExcel')->isClicked()) {
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/novedad/lista", true);
                if($respuesta['error'] == false) {
                    $arrNovedades = $respuesta['novedades'];
                    $this->excel($arrNovedades);
                }
            }
        }
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/novedad/lista");
        if($respuesta->error == false) {
            $arrNovedades = $respuesta->novedades;
        }
        $arrNovedades = $paginator->paginate($arrNovedades, $request->query->getInt('page', 1), 100);

        return $this->render('aplicacion/cliente/transporte/novedad/lista.html.twig',[
            'arNovedades' => $arrNovedades,
            'form'=>$form->createView()
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
            $arrColumnas=['ID', 'GUIA', 'DOCUMENTO','TIPO', 'FECHA', 'DESCRIPCIÓN', 'SOL', 'SOLUCIÓN', 'FECHA'];
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
                $hoja->setCellValue('A' . $j, $arRegistro['codigoNovedadPk']);
                $hoja->setCellValue('B' . $j, $arRegistro['codigoGuiaFk']);
                $hoja->setCellValue('C' . $j, $arRegistro['documentoCliente']);
                $hoja->setCellValue('D' . $j, $arRegistro['novedadTipo']);
                $hoja->setCellValue('E' . $j, $arRegistro['fechaRegistro']);
                $hoja->setCellValue('F' . $j, $arRegistro['descripcion']);
                $hoja->setCellValue('G' . $j, $arRegistro['estadoSolucion'] ? 'SI' : 'NO');
                $hoja->setCellValue('H' . $j, $arRegistro['solucion']);
                $hoja->setCellValue('I' . $j, $arRegistro['estadoSolucion'] ? $arRegistro['fechaSolucion'] : '');
                $j++;
            }
            $libro->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=novedades.xls");
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($libro, 'Xls');
            $writer->save('php://output');
        }else{
            Mensajes::error("No existen registros para exportar");
        }
    }

}