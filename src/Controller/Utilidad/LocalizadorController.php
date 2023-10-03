<?php


namespace App\Controller\Utilidad;

use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocalizadorController  extends AbstractController
{
    /**
     * @Route("/utilidad/localizador/{codigoEmpresa}/{guia}", name="utilidad_localizador")
     */
    public function lista(Request $request, $codigoEmpresa = null, $guia = null,  EntityManagerInterface $em)
    {
        if(!$codigoEmpresa) {
            return $this->redirect($this->generateUrl('principal'));
        }
        $form = $this->createFormBuilder()
            ->add('codigoGuia', TextType::class, ['label' => 'Nombre:', "required"=>false,'attr' => ['placeholder'=>"Guia", "class"=>"input-lg mb-md"] ])
            ->getForm();
        $form->handleRequest($request);
        $arrGuia = [];
        $arrArchivos = [];
        $arrDespachos = [];
        $arrNovedades = [];
        $arrFirmas = null;
        $arrSeguimientos = [];
        $inicio = true;
        $logo = "";
        $url="/transporte/api/oxigeno/guia/detalle";
        $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
        if ($form->isSubmitted()) {
            if( $form->get('codigoGuia')->getData() != ""){
                $parametros['codigoGuia'] =  $form->get('codigoGuia')->getData();
                $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, $url);
                if(!$respuesta->error) {
                    $arrGuia = $respuesta->guia;
                    $arrArchivos = $respuesta->archivos;
                    $arrDespachos = $respuesta->despachos;
                    $arrNovedades = $respuesta->novedades;
                }
                $inicio = false;
            }
        } else {
            if($guia) {
                $parametros = ['codigoGuia' => $guia];
                $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, $url);
                if(!$respuesta->error) {
                    $arrGuia = $respuesta->guia;
                    $arrArchivos = $respuesta->archivos;
                    $arrDespachos = $respuesta->despachos;
                    $arrNovedades = $respuesta->novedades;
                    if ($respuesta->firmas !== null) {
                        $arrFirmas = $respuesta->firmas;
                    }
                    if ($respuesta->seguimientoFirmas !== null) {
                        foreach ($respuesta->seguimientoFirmas as $seguimiento){
                            $data = json_decode($seguimiento->datos);
                            $arrData = (array) $data;
                            array_push($arrSeguimientos, [
                                'cabeceras' => array_keys($arrData),
                                'contenido' => array_values($arrData)
                            ]);
                        }
                    }
                }
            }
        }
        if ($arEmpresa->getLogo()){
            $logo = base64_encode(stream_get_contents($arEmpresa->getLogo()));
        }
        return $this->render('utilidad/localizador.html.twig',[
            'arGuia' => $arrGuia,
            'arrArchivos' => $arrArchivos,
            'arrDespachos' => $arrDespachos,
            'arEmpresa' => $arEmpresa,
            'arrFirmas' => $arrFirmas,
            'arrSeguimientos' => $arrSeguimientos,
            'arrNovedades' => $arrNovedades,
            'logo' => $logo,
            'inicio' => $inicio,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/utilidad/localizadorv2/{codigoEmpresa}/{guia}", name="utilidad_localizadorv2")
     */
    public function listav2(Request $request, $codigoEmpresa = null, $guia = null,  EntityManagerInterface $em)
    {
        if(!$codigoEmpresa) {
            return $this->redirect($this->generateUrl('principal'));
        }
        $form = $this->createFormBuilder()
            ->add('codigoGuia', TextType::class, ['label' => 'Nombre:', "required"=>false,'attr' => ['placeholder'=>"Guia", "class"=>"input-lg mb-md"] ])
            ->getForm();
        $form->handleRequest($request);
        $arrGuia = [];
        $arrArchivos = [];
        $arrDespachos = [];
        $arrFirmas = null;
        $arrSeguimientos = [];
        $inicio = true;
        $logo = "";
        $url="/transporte/api/oxigeno/guia/detalle";
        $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
        if ($form->isSubmitted()) {
            if( $form->get('codigoGuia')->getData() != ""){
                $parametros['codigoGuia'] =  $form->get('codigoGuia')->getData();
                $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, $url);
                if(!$respuesta->error) {
                    $arrGuia = $respuesta->guia;
                    $arrArchivos = $respuesta->archivos;
                    $arrDespachos = $respuesta->despachos;
                }
                $inicio = false;
            }
        } else {
            if($guia) {
                $parametros = ['codigoGuia' => $guia];
                $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, $url);
                if(!$respuesta->error) {
                    $arrGuia = $respuesta->guia;
                    $arrArchivos = $respuesta->archivos;
                    $arrDespachos = $respuesta->despachos;
                    if ($respuesta->firmas !== null) {
                        $arrFirmas = $respuesta->firmas;
                    }
                    if ($respuesta->seguimientoFirmas !== null) {
                        foreach ($respuesta->seguimientoFirmas as $seguimiento){
                            $data = json_decode($seguimiento->datos);
                            $arrData = (array) $data;
                            array_push($arrSeguimientos, [
                                'cabeceras' => array_keys($arrData),
                                'contenido' => array_values($arrData)
                            ]);
                        }
                    }
                }
            }
        }
        if ($arEmpresa->getLogo()){
            $logo = base64_encode(stream_get_contents($arEmpresa->getLogo()));
        }
        return $this->render('utilidad/localizadorv2.html.twig',[
            'arGuia' => $arrGuia,
            'arrArchivos' => $arrArchivos,
            'arrDespachos' => $arrDespachos,
            'arEmpresa' => $arEmpresa,
            'arrFirmas' => $arrFirmas,
            'arrSeguimientos' => $arrSeguimientos,
            'logo' => $logo,
            'inicio' => $inicio,
            'form' => $form->createView()
        ]);
    }
}