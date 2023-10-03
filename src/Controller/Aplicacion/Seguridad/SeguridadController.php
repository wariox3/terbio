<?php


namespace App\Controller\Aplicacion\Seguridad;


use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Entity\Usuario;
use App\Form\Type\Administracion\Empresa\miperfilType;
use App\Form\Type\Seguridad\Usuario\LoginType;
use App\Form\Type\Seguridad\Usuario\RegistroFijoType;
use App\Servicios\Correo;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SeguridadController extends AbstractController
{
    /**
     * @Route("seguridad/registro/{codigoEmpresa}", name="registro", requirements={"codigoEmpresa"="\d+"})
     */
    public function registro(Request $request, $codigoEmpresa = null,  EntityManagerInterface $em)
    {
        $arRegistro = new Usuario;
        $nitEmpresa = "";
        if ($codigoEmpresa != 0) {
            $arEmpresa = $em->find(Empresa::class, $codigoEmpresa);
            if ($arEmpresa) {
                $nitEmpresa = $arEmpresa->getNit();
                unset($arEmpresa);
            }
        }
        $form = $this->createForm(LoginType::class, $arRegistro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $error = false;
                $chkTerminosCondiciones = $request->request->get("chkTerminosCondiciones");
                if ($chkTerminosCondiciones) {
                    $arRegistro->setCodigoUsuarioPk($arRegistro->getNumeroIdentificacion());
                    $booUsuario = $em->getRepository(Usuario::class)->validarNumeroIdentificacion($arRegistro);
                    if ($booUsuario) {
                        if (is_numeric($form->get('numeroIdentificacion')->getData())) {
                            if ($request->request->get("txtNitEmpresa")) {
                                $arEmpresa = $em->getRepository(Empresa::class)->findOneBy(['nit' => $request->request->get("txtNitEmpresa")]);
                                if ($arEmpresa) {
                                    $parametros = ['tipoIdentificacion' => $arRegistro->getIdentificacionRel()->getCodigoIdentificacionPk(), 'numeroIdentificacion' => $arRegistro->getNumeroIdentificacion()];
                                    $booValidarIdentifiacion = FuncionesController::consumirApi($arEmpresa, $parametros, "/recursohumano/api/empleado/validarIdentifiacion");
                                    if ($booValidarIdentifiacion) {
                                        $arRegistro->setEmpresaRel($arEmpresa);
                                        $arRegistro->setEmpleado(true);
                                    } else {
                                        Mensajes::error("El tipo de identificación + número de identificación no están registrados como empleado en la empresa seleccionada");
                                        $error = true;
                                    }
                                } else {
                                    Mensajes::error("No existe empresa con el nit {$request->request->get("txtNitEmpresa")}");
                                    $error = true;
                                }
                            } else {
                                $arRegistro->setEmpresaRel(null);
                            }
                            if ($error == false) {
                                $arRegistro->setClave($arRegistro->getNumeroIdentificacion());
                                $arRegistro->setCodigoRolFk('ROLE_USER');
                                $arRegistro->setFechaRegistro(new \DateTime('now'));
                                $arRegistro->setPersona(true);
                                $em->persist($arRegistro);
                                $em->flush();
                                Mensajes::success("Registro completo, el usuario y contraseña son el número de identificación");
                                if ($codigoEmpresa) {
                                    return $this->redirect($this->generateUrl('login', ['codigoEmpresa' => $codigoEmpresa]));
                                } else {
                                    return $this->redirect($this->generateUrl('login'));
                                }
                            }
                        } else {
                            Mensajes::warning("El número de identificación agregado no es numérico o contiene caracteres que no son permitidos");
                        }
                    }
                } else {
                    Mensajes::warning("Por favor aceptar términos de uso");
                }
            }
        }
        return $this->render('aplicacion/seguridad/registro.html.twig', [
            'form' => $form->createView(),
            'nitEmpresa' => $nitEmpresa,
            'codigoEmpresa' => $codigoEmpresa
        ]);
    }

    /**
     * @Route("seguridad/registrofijo/{codigoEmpresa}", name="registrofijo", requirements={"codigoEmpresa"="\d+"})
     */
    public function registroFijo(Request $request, $codigoEmpresa = null,  EntityManagerInterface $em)
    {
        $arRegistro = new Usuario;
        $nitEmpresa = "";
        if ($codigoEmpresa != 0) {
            $arEmpresa = $em->find(Empresa::class, $codigoEmpresa);
            if ($arEmpresa) {
                $nitEmpresa = $arEmpresa->getNit();
                unset($arEmpresa);
            }
        }
        $form = $this->createForm(RegistroFijoType::class, $arRegistro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $error = false;
                $chkTerminosCondiciones = $request->request->get("chkTerminosCondiciones");
                if ($chkTerminosCondiciones) {
                    $arRegistro->setCodigoUsuarioPk($arRegistro->getNumeroIdentificacion());
                    $arRegistro->setCodigoIdentificacionFk($form->get('identificacionRel')->getData());
                    $booUsuario = $em->getRepository(Usuario::class)->validarNumeroIdentificacion($arRegistro);
                    if ($booUsuario) {
                        if (is_numeric($form->get('numeroIdentificacion')->getData())) {
                            $arEmpresa = $em->getRepository(Empresa::class)->find($codigoEmpresa);
                            if ($arEmpresa) {
                                $parametros = ['tipoIdentificacion' => $arRegistro->getIdentificacionRel()->getCodigoIdentificacionPk(), 'numeroIdentificacion' => $arRegistro->getNumeroIdentificacion()];
                                $booValidarIdentifiacion = FuncionesController::consumirApi($arEmpresa, $parametros, "/recursohumano/api/empleado/validarIdentifiacion");
                                if ($booValidarIdentifiacion) {
                                    $arRegistro->setEmpresaRel($arEmpresa);
                                    $arRegistro->setEmpleado(true);
                                    $arRegistro->setNumeroIdentificacion($form->get('numeroIdentificacion')->getData());
                                } else {
                                    Mensajes::error("El tipo de identificación + número de identificación no están registrados como empleado en la empresa seleccionada");
                                    $error = true;
                                }
                            }

                            if ($error == false) {
                                $parametrosInformacionPersonal = ['tipoIdentificacion' => $arRegistro->getIdentificacionRel()->getCodigoIdentificacionPk(), 'numeroIdentificacion' => $arRegistro->getNumeroIdentificacion()];
                                $url = "/recursohumano/api/empleado/informacion";
                                $respuesta = FuncionesController::consumirApi($arEmpresa, $parametrosInformacionPersonal, $url);
                                if ($respuesta) {
                                    if ($respuesta->error == false) {
                                        $arrEmpleado = $respuesta->empleado;
                                        if ($arrEmpleado->correo) {
                                            $token = bin2hex(random_bytes((10 - (20 % 2)) / 2));
                                            $arRegistro->setClave($token);
                                            $arRegistro->setCodigoRolFk('ROLE_USER');
                                            $arRegistro->setFechaRegistro(new \DateTime('now'));
                                            $arRegistro->setPersona(true);
                                            $arRegistro->setForzarCambioClave(true);
                                            $arRegistro->setCorreo($arrEmpleado->correo);
                                            $arRegistro->setNombres($arrEmpleado->nombre1 . ' ' . $arrEmpleado->nombre2);
                                            $arRegistro->setApellidos($arrEmpleado->apellido1 . ' ' . $arrEmpleado->apellido2);
                                            $em->persist($arRegistro);
                                            $em->flush();
                                            $asunto = "Primera clave Portal Servicios {$arRegistro->getEmpresaRel()->getNombre()}";
                                            $correo = new Correo();
                                            $respuestaCorreo = $correo->enviarCorreo($arrEmpleado->correo, $asunto,
                                                $this->renderView(
                                                    'aplicacion/seguridad/correoPrimeraClave.html.twig',
                                                    array('clave' => $token,
                                                        'nombreEmpresa' => $arRegistro->getEmpresaRel()->getNombre(),
                                                        'usuario' => $arRegistro->getCodigoUsuarioPk())
                                                ));
                                            if ($respuestaCorreo->envio) {
                                                $correoFinal = $this->ocultarCorreo($arrEmpleado->correo);
                                                Mensajes::success("Registro completo, su usuario y contraseña fueron enviados al correo {$correoFinal}");
                                            } else {
                                                Mensajes::error("Error al enviar el correo {$respuestaCorreo->mensajeError}");
                                            }
                                            return $this->redirect($this->generateUrl('login', ['codigoEmpresa' => $codigoEmpresa]));
                                        } else {
                                            Mensajes::error("El correo electrónico registrado en recurso humano es invalido");
                                        }
                                    } else {
                                        Mensajes::error($respuesta->errorMensaje);
                                    }
                                }
                            }
                        } else {
                            Mensajes::warning("El número de identificación agregado no es numérico o contiene caracteres que no son permitidos");
                        }
                    }
                } else {
                    Mensajes::warning("Por favor aceptar términos de uso");
                }
            }
        }
        return $this->render('aplicacion/seguridad/registrofijo.html.twig', [
            'form' => $form->createView(),
            'nitEmpresa' => $nitEmpresa,
            'codigoEmpresa' => $codigoEmpresa
        ]);
    }

    /**
     * @Route("seguridad/recuperarclave/{codigoEmpresa}", name="recuperarclave", requirements={"codigoEmpresa"="\d+"})
     */
    public function recuperarClave(Request $request, $codigoEmpresa = null,  EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->add('codigoUsuario', TextType::class, ['required' => true, 'label' => 'Usuario', 'attr' => ['class' => 'form-control']])
            ->add('btnRecuperar', SubmitType::class, ['label' => 'Recuperar', 'attr' => ['class' => 'btn btn-primary xs']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnRecuperar')->isClicked()) {
                $codigoUsuario = $form->get('codigoUsuario')->getData();
                $recaptcha = $this->recaptcha();
                if ($recaptcha == true) {
                    $arUsuario = $em->getRepository(Usuario::class)->find($codigoUsuario);
                    if ($arUsuario) {
                        $correoElectronico = $arUsuario->getCorreo();
                        if ($correoElectronico) {
                            $asunto = "Recuperar clave Portal Servicios " . ($arUsuario->getCodigoEmpresaFk() ? $arUsuario->getEmpresaRel()->getNombre() : '');
                            $correo = new Correo();
                            $respuestaCorreo = $correo->enviarCorreo($correoElectronico, $asunto,
                                $this->renderView(
                                    'aplicacion/seguridad/correoRecuperarClave.html.twig',
                                    array('clave' => $arUsuario->getClave(),
                                        'nombreEmpresa' => ($arUsuario->getCodigoEmpresaFk() ? $arUsuario->getEmpresaRel()->getNombre() : ''))
                                ));
                            if ($respuestaCorreo->envio) {
                                $correoFinal = $this->ocultarCorreo($correoElectronico);
                                Mensajes::info("Correo enviado a {$correoFinal}");
                            } else {
                                Mensajes::error("Error al enviar el correo {$respuestaCorreo->mensajeError}");
                            }
                        } else {
                            Mensajes::error("El correo es invalido");
                        }
                    } else {
                        Mensajes::error("El usuario no está registrado en el sistema");
                    }
                } else {
                    Mensajes::error("Autentificación fallida.");
                }
            }
        }
        return $this->render('aplicacion/seguridad/recuperarClave.html.twig', [
            'form' => $form->createView(),
            'codigoEmpresa' => $codigoEmpresa
        ]);
    }

    /**
     * @Route("seguridad/usuario/cambiarclave", name="usuario_cambiarclave")
     */
    public function cambiarClave(Request $request,  EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->add('claveActual', PasswordType::class, ['required' => true, 'label' => 'Clave actual', 'attr' => ['class' => 'form-control']])
            ->add('claveNueva', PasswordType::class, ['required' => true, 'label' => 'Clave nueva', 'attr' => ['class' => 'form-control']])
            ->add('claveNuevaRepetir', PasswordType::class, ['required' => true, 'label' => 'Repetir nueva clave', 'attr' => ['class' => 'form-control']])
            ->add('btnCambiar', SubmitType::class, ['label' => 'Cambiar', 'attr' => ['class' => 'btn btn-primary xs']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnCambiar')->isClicked()) {
                $claveActual = $form->get('claveActual')->getData();
                $claveNueva = $form->get('claveNueva')->getData();
                $claveNuevaRepetir = $form->get('claveNuevaRepetir')->getData();
                if ($claveNueva == $claveNuevaRepetir) {
                    $arUsuario = $em->getRepository(Usuario::class)->find($this->getUser()->getCodigoUsuarioPk());
                    if ($claveActual == $arUsuario->getClave()) {
                        $arUsuario->setClave($claveNueva);
                        $arUsuario->setForzarCambioClave(false);
                        $em->persist($arUsuario);
                        $em->flush();
                        Mensajes::success('Se cambió la clave con éxito');
                    } else {
                        Mensajes::error('Su clave actual es incorrecta');
                    }
                } else {
                    Mensajes::error("No coinciden las claves nuevas");
                }
            }
        }
        return $this->render('aplicacion/empleado/usuario/cambiarClave.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("seguridad/usuario/forzarcambioclave", name="usuario_forzarcambioclave")
     */
    public function forzarCambioClave(Request $request,  EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->add('claveActual', PasswordType::class, ['required' => true, 'label' => 'Clave actual', 'attr' => ['class' => 'form-control']])
            ->add('claveNueva', PasswordType::class, ['required' => true, 'label' => 'Clave nueva', 'attr' => ['class' => 'form-control']])
            ->add('claveNuevaRepetir', PasswordType::class, ['required' => true, 'label' => 'Repetir clave nueva', 'attr' => ['class' => 'form-control']])
            ->add('btnCambiar', SubmitType::class, ['label' => 'Cambiar', 'attr' => ['class' => 'btn btn-primary xs']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnCambiar')->isClicked()) {
                $claveActual = $form->get('claveActual')->getData();
                $claveNueva = $form->get('claveNueva')->getData();
                $claveNuevaRepetir = $form->get('claveNuevaRepetir')->getData();
                if ($claveNueva == $claveNuevaRepetir) {
                    $arUsuario = $em->getRepository(Usuario::class)->find($this->getUser()->getCodigoUsuarioPk());
                    if ($claveActual == $arUsuario->getClave()) {
                        $arUsuario->setClave($claveNueva);
                        $arUsuario->setForzarCambioClave(false);
                        $em->persist($arUsuario);
                        $em->flush();
                        Mensajes::success('Se cambió la clave con éxito. Por favor inicia sesión de nuevo');
                        return $this->redirect($this->generateUrl('login', ['codigoEmpresa' => $arUsuario->getCodigoEmpresaFk()]));
                    } else {
                        Mensajes::error('Su clave actual es incorrecta');
                    }
                } else {
                    Mensajes::error("No coinciden las claves nuevas");
                }
            }
        }
        return $this->render('aplicacion/empleado/usuario/forzarcambioclave.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("seguridad/usuario/miperfil/{id}", name="usuario_miperfil")
     */
    public function miPerfil(Request $request, $id,  EntityManagerInterface $em)
    {
        if ($this->getUser()->getCodigoUsuarioPk() != $id) {
            Mensajes::info("Usuario incorrecto al ingresar al perfil");
            return $this->redirect($this->generateUrl('inicio'));
        }
        $arRegistro = $em->getRepository(Usuario::class)->find($id);
        $form = $this->createForm(miperfilType::class, $arRegistro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $arRegistro = $form->getData();
                $em->persist($arRegistro);
                $em->flush();
                Mensajes::success("Información actualizada");
                return $this->redirect($this->generateUrl('usuario_miperfil', ['id' => $id]));
            }
        }
        return $this->render('aplicacion/usuario/miperfil.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function ocultarCorreo($correo)
    {
        $segmento = explode("@", $correo);
        $segmento[0] = substr($correo, 0, 5) . str_repeat("*", strlen($segmento[0]));
        return implode("@", $segmento);
    }

    public function recaptcha()
    {
        $recaptcha_secret = '6LdldrEeAAAAAEsVeRbAjh1FKA6LtYkI8X8NgIpB';
        $recaptcha_response = $_POST['recaptcha_response'];
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        $data = array('secret' => $recaptcha_secret, 'response' => $recaptcha_response, 'remoteip' => $_SERVER['REMOTE_ADDR']);
        $curlConfig = array(CURLOPT_URL => $url, CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_POSTFIELDS => $data);
        $ch = curl_init();
        curl_setopt_array($ch, $curlConfig);
        $response = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($response);
        if ($jsonResponse->success === true && $jsonResponse->score >= 0.5) {
            $respuesta = true;
        } else {
            $respuesta = false;
        }

        return $respuesta;
    }
}