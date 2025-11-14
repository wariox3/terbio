<?php


namespace App\Controller\Aplicacion\Empleado\informacionPersonal;


use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class informacionPersonal extends AbstractController
{
    #[Route("empleado/informacionpersonal", name:"empleado_informacion_personal")]
    public function informacionPersonal(Request $request)
    {
        $arUsuario = $this->getUser();
        $parametrosInformacionPersonal = ["tipoIdentificacion" => $arUsuario->getCodigoIdentificacionFK(), 'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        $parametrosReferenciasPersonales = ['numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(), "identificacion" => $arUsuario->getCodigoIdentificacionFK()];
        $urlInformacionEmpleado = "/recursohumano/api/empleado/informacionEmpleado";
        $urlReferenciaspersonales = "/api/recursohumano/empleado/referenciaspersonales/lista";
        $urlCiudad = '/api/general/ciudad/lista';
        $arInformacionPersonal = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosInformacionPersonal, $urlInformacionEmpleado);
        $arReferenciasPersonales = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosReferenciasPersonales, $urlReferenciaspersonales);
        $arrCiudades = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [], $urlCiudad);
        $arCiudades = $this->fuenteChoiceCiudad($arrCiudades->ciudades);
        $arrEmpleado = $arInformacionPersonal->empleado;
        $arrReferenciasPersonales = [];
        if ($arReferenciasPersonales->error == false) {
            $arrReferenciasPersonales = $arReferenciasPersonales->referenciasPersonales;
        } else {
            Mensajes::error("Error: {$arReferenciasPersonales->errorMensaje}");
        }
        $form = $this->createFormBuilder()
            ->add('codigoEmpleado', HiddenType::class, array('required' => false, 'data' => $arrEmpleado->codigoEmpleadoPk ?? null))
            ->add('telefono', TextType::class, array('required' => false, 'data' => $arrEmpleado->telefono ?? null))
            ->add('celular', TextType::class, array('required' => false, 'data' => $arrEmpleado->celular ?? null))
            ->add('barrio', TextType::class, array('required' => false, 'data' => $arrEmpleado->barrio ?? null))
            ->add('direccion', TextType::class, array('required' => false, 'data' => $arrEmpleado->direccion ?? null))
            ->add('correo', TextType::class, array('required' => false, 'data' => $arrEmpleado->correo ?? null))
            ->add('ciudadRel', ChoiceType::class, ['choices' => $arCiudades, 'data' => $arrEmpleado->codigoCiudadFk ?? null, 'attr' => ['class' => 'aplicarSelect2']])
            ->add('btnActualizar', SubmitType::class, ['label' => 'Actualizar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->get('btnActualizar')->isClicked()) {
                $parametrosActualixarInformacionPersonal = [
                    'codigoEmpleado' => $form->get('codigoEmpleado')->getData(),
                    'telefono' => $form->get('telefono')->getData(),
                    'celular' => $form->get('celular')->getData(),
                    'barrio' => $form->get('barrio')->getData(),
                    'direccion' => $form->get('direccion')->getData(),
                    'correo' => $form->get('correo')->getData(),
                    "codigoCiudad" => $form->get('ciudadRel')->getData(),
                ];
                $urlActualizarInformacionPersonal = "/recursohumano/api/empleado/actualizarinformacion";
                $actualizarInformacionPersonal = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosActualixarInformacionPersonal, $urlActualizarInformacionPersonal);
                if ($actualizarInformacionPersonal->error == false) {
                    Mensajes::info("Gracias por actualizar la información");
                    $this->redirect($this->generateUrl('empleado_informacion_personal'));
                } else {
                    Mensajes::error("Error: {$actualizarInformacionPersonal->errorMensaje}");
                }
            }
            if ($request->get('OpActualizar')) {
                $codigo = $request->get('OpActualizar');
                $arrControles = $request->request->all();
                $arrTalla = $arrControles['arrTalla'];
                $talla = $arrTalla[$codigo];
                $parametros = [
                    'codigoEmpleadoTalla' => $codigo,
                    'talla' =>$talla
                ];
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, '/api/recursohumano/empleadotalla/actualizar');
            }
        }

        $arrEmpleadosTallas = [];
        $arEmpleadoTallas = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), ['numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()], '/api/recursohumano/empleadotalla/lista');
        if ($arEmpleadoTallas->error == false) {
            $arrEmpleadosTallas = $arEmpleadoTallas->tallas;
        } else {
            Mensajes::error("Error: {$arEmpleadoTallas->errorMensaje}");
        }
        return $this->render('aplicacion/empleado/informacionPersonal/nuevo.html.twig', [
            'arInformacionPersonal' => $arInformacionPersonal,
            'arrEmpleado' => $arrEmpleado,
            'arrReferenciasPersonales' => $arrReferenciasPersonales,
            'arrEmpleadosTallas' => $arrEmpleadosTallas,
            'form' => $form->createView()
        ]);
    }


    #[Route("empleado/referenciaspersonales/nuevo/{id}/{codigoReferencia}", name:"empleado_referenciaspersonales_personal")]
    public function detalleNuevo(Request $request, $id, $codigoReferencia)
    {
        $arUsuario = $this->getUser();
        $urlCiudad = '/api/general/ciudad/lista';
        $arrCiudades = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [], $urlCiudad);
        $arCiudades = $this->fuenteChoiceCiudad($arrCiudades->ciudades);
        $form = $this->createFormBuilder()
            ->add('ciudadRel', ChoiceType::class, ['choices' => $arCiudades, 'attr' => ['class' => 'aplicarSelect2'], 'data' => ""])
            ->add('nombre', TextType::class, ['required' => false, 'data' => ""])
            ->add('telefono', TextType::class, ['required' => false, 'data' => ""])
            ->add('ocupacion', TextType::class, ['required' => false, 'data' => ""])
            ->add('direccion', TextType::class, ['required' => false, 'data' => ""])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametrosReferenciaPersonal = [
                    "numeroIdentificacion" => $arUsuario->getNumeroIdentificacion(),
                    "identificacion" => $arUsuario->getCodigoIdentificacionFK(),
                    "codigoCiudad" => $form['ciudadRel']->getData(),
                    "nombre" => $form['nombre']->getData(),
                    "telefono" => $form['telefono']->getData(),
                    "ocupacion" => $form['ocupacion']->getData(),
                    "direccion" => $form['direccion']->getData(),
                ];
                $url = '/api/recursohumano/empleado/referenciaspersonales/nuevo';
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosReferenciaPersonal, $url);
                if ($respuesta->error == false) {
                    Mensajes::success("Referencia personal registrada");
                    echo "<script  type='text/javascript'>window.close();window.opener.location.reload();</script>";
                } else {
                    Mensajes::error($respuesta->errorMensaje);
                }
            }
        }

        return $this->render('aplicacion/empleado/informacionPersonal/referenciaPersonal.html.twig', [
            'form' => $form->createView()
        ]);
    }


    private function fuenteChoiceCiudad($datos)
    {

        $arrDatos = ['' => ''];
        foreach ($datos as $dato) {
            $arrDatos["{$dato->nombre} - {$dato->departamentoNombre} "] = $dato->codigoCiudadPk;
        }
        return $arrDatos;
    }
}