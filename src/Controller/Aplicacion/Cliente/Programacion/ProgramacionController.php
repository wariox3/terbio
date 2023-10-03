<?php


namespace App\Controller\Aplicacion\Cliente\Programacion;


use App\Controller\FuncionesController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProgramacionController extends AbstractController
{
    /**
     * @Route("/cliente/programacion/lista", name="cliente_programacion_lista")
     */
    public function inicioAction(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $fechaActual = new \DateTime('now');
        $mesActualFijo = $fechaActual->format('Y-m');
        $mesActual = $fechaActual->format('Y-m');
        $url="/turno/api/programacion/cliente";
        $parametros = [
            'codigoCliente'=> $arUsuario->getCodigoTerceroErpFk(),
            'fecha' => [
                "anio"=> $fechaActual->format("Y"),
                "mes"=> $fechaActual->format("n")
            ]
        ];
        $form = $this->createFormBuilder()
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnFiltro')->isClicked()) {
                $filtroMes = new \DateTime($request->request->get("mes"));
                $parametros['fecha']["mes"]= $filtroMes->format("n");
                $parametros['fecha']["anio"]= $filtroMes->format("Y");
                $mesActual = $filtroMes->format('Y-m');
            }
        }
        $arrRegistros = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        $arrProgramaciones = $paginator->paginate($arrRegistros, $request->query->getInt('page', 1), 50);
        return $this->render('aplicacion/cliente/programacion/lista.html.twig',[
            "form"=>$form->createView(),
            "mesActual"=>$mesActual,
            "mesActualFijo"=>$mesActualFijo,
            'arrProgramaciones' => $arrProgramaciones
        ]);
    }

    private function fechasProgramacion() {
        $arrFecha = [];
        $fecha = new \DateTime('now');

        //Mes siguiente
        $anio = $fecha->format('Y');
        $mes = $fecha->format('n');
        if($mes == 12) {
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
        if($mes == 1) {
            $mes = 12;
            $anio -= 1;
        } else {
            $mes -= 1;
        }
        $arrFecha[] = ['anio' => $anio, 'mes' => $mes, 'actual' => '0'];
        return $arrFecha;
    }
}