<?php


namespace App\Controller\Utilidad;

use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Entity\RespuestaElectronico;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacturaController extends AbstractController
{
    /**
     * @Route("/utilidad/factura/{codigoEmpresa}/{modelo}/{codigoFactura}/{respuestaOrigen}", name="utilidad_factura")
     */
    public function lista(Request $request, EntityManagerInterface $em, $codigoEmpresa, $modelo, $codigoFactura, $respuestaOrigen = null)
    {
        if ($codigoEmpresa && $modelo && $codigoFactura) {
            $form = $this->createFormBuilder()
                ->getForm();
            $form->handleRequest($request);
            $parametrosConsulta = [
                'modelo' => $modelo,
                'codigoFactura' => $codigoFactura,
            ];
            $url = "/general/api/factura/lista";
            $arFactura = null;
            $arGuias = null;
            $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
            $respuesta = FuncionesController::consumirApi($arEmpresa, $parametrosConsulta, $url);
            $expirado = false;
            if ($respuesta->error == false) {
                $arFactura = $respuesta->factura;
                if (isset($respuesta->factura->guias)) {
                    $arGuias = $respuesta->factura->guias;
                }

            }
            if ($form->isSubmitted()) {

            }
            $logo = "";
            if ($arEmpresa->getLogo()) {
                $logo = base64_encode(stream_get_contents($arEmpresa->getLogo()));
            }
            return $this->render('utilidad/factura.html.twig', [
                'codigoEmpresa' => $codigoEmpresa,
                'arEmpresa' => $arEmpresa,
                'arFactura' => $arFactura,
                'arGuias' => $arGuias,
                'logo' => $logo,
                'expirado' => $expirado,
                'form' => $form->createView()
            ]);
        } else {
            return $this->redirect($this->generateUrl('principal'));
        }
    }

    /**
     * @Route("/utilidad/factura/{codigoEmpresa}/{codigoGuia}/", name="utilidad_factura_archivos_masivos")
     */
    public function descarMasivos(Request $request, $codigoEmpresa, $codigoGuia,  EntityManagerInterface $em)
    {
        if ($codigoEmpresa && $codigoGuia) {
            //$arUsuario = $this->getUser();
            $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
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
                $respuesta = FuncionesController::consumirApi($arEmpresa, [
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
            $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, "/transporte/api/oxigeno/archivosmasivos/lista");
            if ($respuesta->error == false) {
                $arrMasivos = $respuesta->arrMasivos;
            }
            return $this->render('utilidad/archivosMasivos.html.twig', [
                'arrMasivos' => $arrMasivos,
                'form' => $form->createView()
            ]);
        } else {
            return $this->redirect($this->generateUrl('principal'));
        }

    }


    /**
     * @Route("utilidad/descargarguia/{codigoEmpresa}/{codigoGuia}", name="descargarguia")
     */
    public function descargarGuia($codigoEmpresa, $codigoGuia, EntityManagerInterface $em)
    {
        $parametrosConsulta = [
            'codigoGuia' => $codigoGuia
        ];
        $url = "/api/transporte/guia/detalle";
        $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
        $respuesta = FuncionesController::consumirApi($arEmpresa, $parametrosConsulta, $url);
        if ($respuesta) {
            if (isset($respuesta->archivoMasivo)) {
                if (count($respuesta->archivoMasivo)) {
                    $archivo = $respuesta->archivoMasivo[0];
                    $fileContent = base64_decode($archivo->base64);
                    header('Content-Type: ' . $archivo->tipo);
                    header('Content-Disposition: attachment; filename="' . $archivo->nombre . '"');
                    echo $fileContent;
                }
            }
        }
        echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";;
    }
}