<?php


namespace App\Controller\Utilidad;

use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacturaController extends AbstractController
{
    #[Route("/utilidad/factura/{codigoEmpresa}/{modelo}/{codigoFactura}/{respuestaOrigen}", name:"utilidad_factura")]
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

    #[Route("/utilidad/guia/fichero/{codigoEmpresa}/{codigoGuia}/", name:"utilidad_guia_fichero")]
    public function guiaFicheros(Request $request, $codigoEmpresa, $codigoGuia,  EntityManagerInterface $em)
    {
        if ($codigoEmpresa && $codigoGuia) {
            $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
            $form = $this->createFormBuilder()
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $codigoFichero = $request->request->get('btnDescargar');
                $parametros = [
                    'codigo' => $codigoFichero
                ];
                $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, "/api/documental/fichero/descarga");
                if ($respuesta->error == false) {
                    $fileContent = base64_decode($respuesta->base64);
                    header('Content-Type: ' . $respuesta->tipo);
                    header('Content-Disposition: attachment; filename="' . $respuesta->nombre . '"');
                    echo $fileContent;
                } else {
                    Mensajes::error(utf8_decode($respuesta->errorMensaje));
                }
            }
            $ficheros = [];
            $parametros = [
                'codigo' => $codigoGuia,
                'codigoModelo' => 'TteGuia'
            ];
            $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, "/api/documental/fichero/lista");
            if ($respuesta->error == false) {
                $ficheros = $respuesta->ficheros;
            }
            return $this->render('utilidad/ficheros.html.twig', [
                'ficheros' => $ficheros,
                'form' => $form->createView()
            ]);
        } else {
            return $this->redirect($this->generateUrl('principal'));
        }

    }

}