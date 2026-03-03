<?php

namespace App\Controller;

use App\Entity\Configuracion;
use App\Entity\Empresa;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{
    #[Route('/', name:'principal')]
    public function principalAction(EntityManagerInterface $em)
    {
        $arConfiguracion = $em->getRepository(Configuracion::class)->find(1);
        if ($arConfiguracion->getCodigoEmpresaPrincipal()) {
            return $this->redirect($this->generateUrl('login', ['codigoEmpresa' => $arConfiguracion->getCodigoEmpresaPrincipal()]));
        } else {
            return $this->render('principal.twig');
        }
    }

    #[Route("/terminoscondiciones", name:"terminoscondiciones")]
    public function terminosCondicionesAction(Request $request)
    {
        return $this->render('terminoscondiciones.twig', [
        ]);
    }

    #[Route("/inicio", name:"inicio")]
    public function inicio(Request $request,  EntityManagerInterface $em)
    {
        $usuario = $this->getUser();
        $arUsuario = $em->getRepository(Usuario::class)->find($usuario);
        $arEmpresa = $arUsuario->getEmpresaRel();
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($request->get('OpDescargar')){
                $this->ficheroDescarga($em, $arEmpresa->getCodigoEmpresaPk(), $request->get('OpDescargar'));
            }
        }
        if($arEmpresa){
            if ($arEmpresa->isValidarContratoActivo()){
                if($arUsuario->getEmpleado()){
                    $url = "/recursohumano/api/empleado/validarcontratiactivo";
                    $parametros = ['numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(), 'tipoIdentificacion' => $arUsuario->getCodigoIdentificacionFk()];
                    $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
                    if($respuesta !== null){
                        if ($respuesta->error === false) {
                            if($respuesta->validaContrato === false){
                                $arUsuario->setEmpresaRel(null);
                                $em->persist($arUsuario);
                                $em->flush();
                                return $this->redirect($this->generateUrl('inicio'));
                            }
                        }
                    }
                }
            }
        }
        if ($arUsuario->isForzarCambioClave()) {
            return $this->redirect($this->generateUrl('usuario_forzarcambioclave'));
        }
        $booCliente = null;
        $booEmpleado = null;
        $booProveedor = null;
        $booEmpresa = null;
        $arrTurnos = null;
        $arrRecurso = null;
        $arrCapacitaciones = null;
        $codigoPuesto = null;
        $arrInformacionCapacitaciones = null;
        $arrEnlaces = null;
        if ($usuario->getEmpresaRel() != null) {
            $booCliente = $usuario->getCliente();
            $booEmpleado = $usuario->getEmpleado();
            $booProveedor = $usuario->getProveedor();
            $booEmpresa = $usuario->getEmpresa();
            if($usuario->getEmpleado()) {
                if($usuario->getEmpresaRel()->isMenuEmpleadoProgramacion()) {
                    $respuesta = $this->turnos();
                    if ($respuesta['turnos']) {
                        $arrTurnos = $respuesta['turnos'];
                        $codigoPuesto = $arrTurnos[0]->codigoPuestoFk;
                    }
                    if ($respuesta['recurso']) {
                        $arrRecurso = $respuesta['recurso'];
                    }
                    $arrCapacitaciones = $this->capacitacionesPendientes();
                    if ($arrCapacitaciones) {
                        $arrInformacionCapacitaciones = $this->archivosCapacitacitaciones($arrCapacitaciones);
                    }
                }
                $arrEnlaces = $this->enlaces();
            }
        }
        return $this->render('aplicacion/inicio.html.twig', [
            'arrTurnos' => $arrTurnos,
            'arrRecurso' => $arrRecurso,
            'booCliente' => $booCliente,
            'booEmpleado' => $booEmpleado,
            'booProveedor' => $booProveedor,
            'booEmpresa' => $booEmpresa,
            'codigoPuesto' => $codigoPuesto,
            'arrInformacionCapacitaciones' => $arrInformacionCapacitaciones,
            'arrEnlaces' => $arrEnlaces,
//            'arrTurnos' => [],
//            'arrRecurso' => [],
//            'booCliente' => [],
//            'booEmpleado' => [],
//            'booProveedor' => [],
//            'booEmpresa' => [],
//            'codigoPuesto' => [],
//            'arrInformacionCapacitaciones' => [],
//            'arrEnlaces' => [],
            'form' => $form->createView()
        ]);
    }

    #[Route("/descargar/archivo/{tipo}/{codigoArchivo}", name:"descargar_archivo")]
    public function descargarArchivo(Request $request, $tipo, $codigoArchivo)
    {
        $arUsuario = $this->getUser();
        $parametros = [
            'tipo' => $tipo,
            'codigo' => $codigoArchivo
        ];
        $url = "/documental/api/archivo/descargar";
        $arrArchivo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if ($arrArchivo) {
            $arrArchivo = $arrArchivo[0];
            if ($arrArchivo->error == 0) {
                $ruta = '/var/www/html/temporal/oxigeno_' . $arrArchivo->nombre;
                $file = fopen($ruta, "wb");
                fwrite($file, base64_decode($arrArchivo->base64));
                fclose($file);
                $response = new Response();
                $response->headers->set('Cache-Control', 'private');
                $response->headers->set('Content-type', 'application/pdf');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $arrArchivo->nombre . '";');
                //$response->headers->set('Content-length', $arArchivo->getTamano());
                $response->sendHeaders();
                $response->setContent(readfile($ruta));
                return $response;
            }
        }
    }

    private function turnos()
    {
        $arUsuario = $this->getUser();
        $fecha = new \DateTime('now');
        $anio = $fecha->format('Y');
        $mes = $fecha->format('n');
        $dia = $fecha->format('j');
        $parametros = [
            'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(),
            'anio' => $anio,
            'mes' => $mes,
            'dia' => $dia
        ];
        $arrTurnos = [];
        $arrRecurso = [];

        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/turno/api/programacion/turno");

        if ($respuesta !== null &&
            is_object($respuesta) &&
            property_exists($respuesta, 'error') &&
            $respuesta->error == false) {

            if (property_exists($respuesta, 'turnos')) {
                $arrTurnos = $respuesta->turnos;
            }
            if (property_exists($respuesta, 'recurso')) {
                $arrRecurso = $respuesta->recurso;
            }
        }

        return [
            'turnos' => $arrTurnos,
            'recurso' => $arrRecurso
        ];
    }

    private function capacitacionesPendientes()
    {
        $arUsuario = $this->getUser();
        $arrCapacitaciones = [];
        $parametros = ['numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        $url = "/recursohumano/api/capacitaciones/empleado/pendiente";
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if($respuesta->error == false ) {
            $arrCapacitaciones = $respuesta->capacitaciones;
        }
        return $arrCapacitaciones;
    }

    private function enlaces()
    {
        $arUsuario = $this->getUser();
        $arrEnlaces = [];
        $parametros = [];
        $url = "/api/general/enlace/lista";

        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if ($respuesta !== null &&
            is_object($respuesta) &&
            property_exists($respuesta, 'error') &&
            $respuesta->error == false &&
            property_exists($respuesta, 'enlaces')) {
            $arrEnlaces = $respuesta->enlaces;
        }

        return $arrEnlaces;
    }

    private function archivosPuesto($codigoPuesto)
    {

        $arUsuario = $this->getUser();
        $parametros = [
            'tipo' => "TurPuesto",
            'codigo' => $codigoPuesto
        ];
        $url = "/documental/api/archivo/lista";
        $arrArchivo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $arrArchivo;
    }

    private function archivosCapacitacitaciones($objCapacitaciones)
    {
        $arUsuario = $this->getUser();
        $url = "/api/documental/fichero/lista";
        $arrCapacitacionesConarchivos = [];
        if (!isset($objCapacitaciones->error)) {
            foreach ($objCapacitaciones as $objCapacitacion) {
                $parametros = [
                    'codigoModelo' => 'RhuCapacitacion',
                    'codigo' => $objCapacitacion->codigoCapacitacionPk
                ];
                $arrCapacitaciones = (array)$objCapacitacion;
                $arrCapacitaciones['archivos'] = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
                array_push($arrCapacitacionesConarchivos, $arrCapacitaciones);
            }
        }
        return $arrCapacitacionesConarchivos;
    }

    #[Route("inicio/fichero/descargar/{codigoEmpresa}/{codigo}", name:"utilidad_fichero_descarga")]
    public function ficheroDescarga(EntityManagerInterface $em, $codigoEmpresa, $codigo)
    {
        $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
        $parametros = [
            'codigo' => $codigo
        ];
        $respuesta = FuncionesController::consumirApi($arEmpresa, $parametros, "/api/documental/fichero/descarga");
        if ($respuesta->error == false) {
            $fileContent = base64_decode($respuesta->base64);
            header('Content-Type: ' . $respuesta->tipo);
            header('Content-Disposition: attachment; filename="' . $respuesta->nombre . '"');
            echo $fileContent;
        }
        echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";;
    }
}