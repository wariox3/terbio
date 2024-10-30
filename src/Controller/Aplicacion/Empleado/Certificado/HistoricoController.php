<?php

namespace App\Controller\Aplicacion\Empleado\Certificado;

use App\Controller\FuncionesController;
use App\Formato\CertificadoLaboral;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoricoController extends AbstractController
{

    #[Route("empleado/certificadohistorico/lista", name:"certificadohistorico_lista")]
    public function lista(Request $request,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('btnImprimir', SubmitType::class, ['label' => 'Imprimir', 'attr' => ['class' => 'btn btn-sm btn-default']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnImprimir')->isClicked()) {
                $parametrosImprimir = [
                    'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()
                ];
                $url = "/recursohumano/api/oxigeno/certificadolaboralhistorico/imprimir";
                $respuestaFormato = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosImprimir, $url);
                if ($respuestaFormato->error == false) {
                    $archivo = "/var/www/html/temporal/certificadoLaboralHistorico{$arUsuario->getNumeroIdentificacion()}.pdf";
                    $nombreArchivo = "certificadoLaboralHistorico{$arUsuario->getNumeroIdentificacion()}.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuestaFormato->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', "attachment; filename=$nombreArchivo;");
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
        }
        return $this->render('aplicacion/empleado/certificado/historico.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
