<?php


namespace App\Controller\Aplicacion\Cliente\Transporte\Gestion;

use App\Controller\FuncionesController;
use App\Formato\Guias;
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

class GuiaSoporteController  extends AbstractController
{
    #[Route("/cliente/transporte/gestion/guia/soporte", name:"cliente_transporte_gestion_guia_soporte")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em )
    {
        $arUsuario = $this->getUser();
        $arrGuias = [];
        $form = $this->createFormBuilder()
            ->add('codigoDespacho', IntegerType::class, array('required' => true))
            ->add('btnSoporte', SubmitType::class, array('label' => 'Soporte'))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        $parametros = [];
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnFiltro')->isClicked() || $form->get('btnSoporte')->isClicked()) {
                $parametros['codigoDespacho'] = $form->get('codigoDespacho')->getData();

            }
            if ($form->get('btnSoporte')->isClicked()) {
                $arrSeleccionados = $request->request->get('ChkSeleccionar');
                if($arrSeleccionados) {
                    $parametros['guias'] = $arrSeleccionados;
                    $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/guia/soporte");
                    if($respuesta){
                        if($respuesta->error == true) {
                            Mensajes::error($respuesta->errorMensaje);;
                        }
                    }
                }
            }

        }
        if($parametros) {
            $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/guia/soporte/pendiente");
            if($respuesta != null){
                if($respuesta->error == false) {
                    $arrGuias = $respuesta->guias;
                }
            }
        }
        return $this->render('aplicacion/cliente/transporte/gestion/guiaSoporte.html.twig',[
            'arGuias' => $arrGuias,
            'form'=>$form->createView()
        ]);
    }

}