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

class RedespachoController  extends AbstractController
{
    #[Route("/cliente/transporte/gestion/guia/redespacho", name:"cliente_transporte_gestion_guia_redespacho")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em )
    {
        $arUsuario = $this->getUser();
        $arrDatos = [];
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [], "/transporte/api/oxigeno/redespachomotivo/lista", true);
        if($respuesta) {
            if($respuesta['error'] == false) {
                $arrDatos = $respuesta['redespachoMotivos'];
            }
        }
        $arrRedespachoMotivos = $this->fuenteChoiceRedespachoMotivo($arrDatos);
        $form = $this->createFormBuilder()
            ->add('codigoGuia', IntegerType::class, array('required' => false))
            ->add('documentoCliente', TextType::class, array('required' => false))
            ->add('redespachoMotivoRel', ChoiceType::class, array('choices' => $arrRedespachoMotivos))
            ->add('btnRedespacho', SubmitType::class, array('label' => 'Redespacho'))
            ->getForm();
        $form->handleRequest($request);
        $parametros = [];
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnRedespacho')->isClicked()) {
                $parametros = [
                    'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                    'codigoGuia' => $form->get('codigoGuia')->getData(),
                    'documentoCliente' => $form->get('documentoCliente')->getData(),
                    'codigoRedespachoMotivo' => $form->get('redespachoMotivoRel')->getData()
                ];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/guia/redespacho");
                if($respuesta){
                    if($respuesta->error == true) {
                        Mensajes::error($respuesta->errorMensaje);;
                    }
                } else {
                    Mensajes::info("Se activo el redespacho exitosamente");
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
        return $this->render('aplicacion/cliente/transporte/gestion/redespacho.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    private function fuenteChoiceRedespachoMotivo($datos) {
        foreach ($datos as $dato) {
            $arrDatos["{$dato['nombre']}"] = $dato['codigoRedespachoMotivoPk'];
        }
        return $arrDatos;
    }
}