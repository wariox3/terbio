<?php


namespace App\Controller\Aplicacion\Empresa\Usuario;


use App\Controller\FuncionesController;
use App\Entity\Identificacion;
use App\Entity\Usuario;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioController extends AbstractController
{
    #[Route("/empresa/usuario/lista", name:"empresa_usuario_lista")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $session = new Session();
        $raw = ['filtros' => $session->get('filtroUsuarios')];
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('codigoRolFk', ChoiceType::class, ['choices' => ['Todos' => '',
                'Cliente' => 'cliente',
                'Proveedor' => 'proveedor'],
                'required' => false,
                'data' => isset($raw['filtros']['rol']) ? $raw['filtros']['rol'] : null])
            ->add('codigoUsuario', TextType::class, array('required' => false, 'data' => isset($raw['filtros']['codigoUsuario']) ? $raw['filtros']['codigoUsuario'] : null))
            ->add('numeroIdentificacion', TextType::class, array('required' => false, 'data' => isset($raw['filtros']['numeroIdentificacion']) ? $raw['filtros']['numeroIdentificacion'] : null))
            ->add('nombre', TextType::class, array('required' => false, 'data' => isset($raw['filtros']['nombre']) ? $raw['filtros']['nombre'] : null))
            #->add('btnActualizarDatosUsuario', SubmitType::class, array('label' => 'Actualizar datos usuario'))
            ->add('btnExcel', SubmitType::class, ['label' => 'Excel', 'attr' => ['class' => 'btn btn-sm btn-default']])
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        $raw = [];
        if ($form->isSubmitted()) {
            if ($form->get('btnFiltro')->isClicked() || $form->get('btnExcel')->isClicked()) {
                $raw['filtros'] = $this->filtros($form);
            }
            if ($form->isSubmitted() && $form->isValid()) {
                if ($request->request->get('OpEliminar')) {
                    $codigo = $request->request->get('OpEliminar');
                    $em->getRepository(Usuario::class)->eliminar([$codigo]);
                }
            }
            if ($form->get('btnExcel')->isClicked()) {
                set_time_limit(0);
                ini_set("memory_limit", -1);
                $arrRegistros = $em->getRepository(Usuario::class)->lista($raw);
                $this->excelLista($arrRegistros, $em);
                /*$url = "/api/recursohumano/empleado/lista/datosempleado";
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), [], $url, true);
                if ($respuesta['error'] == false) {
                    $arrEmpleados = $respuesta['empleados'];

                }*/
            }

            /*if ($form->get('btnActualizarDatosUsuario')->isClicked()) {
                set_time_limit(0);
                ini_set("memory_limit", -1);
                $em->getRepository(Usuario::class)->actualizarDatosUsuario();
            }*/
        }
        $arrRegistros = $em->getRepository(Usuario::class)->lista($raw);
        $arUsuarios = $paginator->paginate($arrRegistros, $request->query->getInt('page', 1), 40);
        return $this->render('aplicacion/empresa/usuario/lista.html.twig', [
            'arUsuarios' => $arUsuarios,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/empresa/usuario/nuevo/{id}", name:"empresa_usuario_nuevo")]
    public function nuevo(Request $request, $id,  EntityManagerInterface $em)
    {
        $arUsuario = new Usuario;
        $arEmpresa = $this->getUser()->getEmpresaRel();
        $ingresarContrasena = true;
        $propiedades = ['required' => true, 'label' => 'Clave'];
        if ($id != "" && $id != "0") {
            $arUsuario = $em->getRepository(Usuario::class)->find($id);
            if (!$arUsuario) {
                Mensajes::error("No existe");
            } else {
                $propiedades = ['attr' => ['readonly' => 'readonly']];
                $ingresarContrasena = false;
            }
        }
        $form = $this->createFormBuilder()
            ->add('identificacionRel', EntityType::class, [
                'required' => true,
                'class' => Identificacion::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
                'label' => 'Tipo documento:',
                'attr' => ['class' => 'form-control'],
                'data' => ($arUsuario->getCodigoIdentificacionFk()) ? $em->getReference(Identificacion::class, $arUsuario->getCodigoIdentificacionFk()) : null
            ])
            ->add('codigoUsuarioPk', TextType::class, ['data' => $arUsuario->getCodigoUsuarioPk(), 'disabled' => $arUsuario->getCodigoUsuarioPk() ? true : false, 'required' => true, 'label' => 'Usuario', 'attr' => ['class' => 'form-control']])
            ->add('numeroIdentificacion', IntegerType::class, ['data' => $arUsuario->getNumeroIdentificacion(), 'required' => true, 'label' => 'Número idenficación', 'attr' => ['class' => 'form-control']])
            ->add('nombres', TextType::class, ['data' => $arUsuario->getNombres(), 'required' => true, 'label' => 'Nombres', 'attr' => ['class' => 'form-control']])
            ->add('apellidos', TextType::class, ['data' => $arUsuario->getApellidos(), 'required' => false, 'label' => 'Apellidos', 'attr' => ['class' => 'form-control']])
            ->add('correo', EmailType::class, ['data' => $arUsuario->getCorreo(), 'required' => true, 'label' => 'Correo electrónico', 'attr' => ['class' => 'form-control']])
            ->add('cliente', CheckboxType::class, array('data' => $arUsuario->getCliente(), 'required' => false))
            ->add('proveedor', CheckboxType::class, array('data' => $arUsuario->getProveedor(), 'required' => false))
            ->add('gestionTranporte', CheckboxType::class, array( 'data' => $arUsuario->isGestionTranporte(), 'label'=> 'Gestión Transporte', 'required' => false))
            ->add('guiaNuevo', CheckboxType::class, array('data' => $arUsuario->isGuiaNuevo(), 'label'=> 'Guía nuevo', 'required' => false))
            ->add('cambiarValoresGuia', CheckboxType::class, array('data' => $arUsuario->isCambiarValoresGuia(), 'required' => false))
            ->add('permiteCambiarAdquiriente', CheckboxType::class, array('data' => $arUsuario->isPermiteCambiarAdquiriente(), 'required' => false))
            ->add('estadoRecogido', CheckboxType::class, array('data' => $arUsuario->isEstadoRecogido(), 'required' => false))
            ->add('estadoIngreso', CheckboxType::class, array('data' => $arUsuario->isEstadoIngreso(), 'required' => false))
            ->add('bloquearRecaudo', CheckboxType::class, array('data' => $arUsuario->isBloquearRecaudo(), 'label'=> 'Bloquear recaudo', 'required' => false))
            ->add('bloquearAdquirienteCredito', CheckboxType::class, array('data' => $arUsuario->isBloquearAdquirienteCredito(), 'label'=> 'Bloquear adquiriente credito', 'required' => false))
            ->add('invertirOrigenDestino', CheckboxType::class, array('data' => $arUsuario->isInvertirOrigenDestino(), 'label'=> 'Invertir origen/destino', 'required' => false))
            ->add('codigoOperacionClienteFk', TextType::class, ['data' => $arUsuario->getCodigoOperacionClienteFk(), 'required' => false, 'label' => '', 'attr' => ['class' => 'form-control']])
            ->add('codigoTerceroErpFk', TextType::class, ['data' => $arUsuario->getCodigoTerceroErpFk(), 'required' => true, 'label' => '', 'attr' => ['class' => 'form-control']])
            ->add('codigoOperacionFk', TextType::class, ['data' => $arUsuario->getCodigoOperacionFk(), 'required' => true, 'label' => 'Operacion', 'attr' => ['class' => 'form-control']])
            ->add('codigoCiudadOrigenFk', TextType::class, ['data' => $arUsuario->getCodigoCiudadOrigenFk(), 'required' => false, 'label' => 'Codigo ciudad origen', 'attr' => ['class' => 'form-control']])
            ->add('direccionRemitente', TextType::class, ['data' => $arUsuario->getDireccionRemitente(), 'required' => false, 'label' => 'Direccion remitente:', 'attr' => ['class' => 'form-control']])
            ->add('telefonoRemitente', TextType::class, ['data' => $arUsuario->getTelefonoRemitente(), 'required' => false, 'label' => 'Telefono remitente:', 'attr' => ['class' => 'form-control']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->add('claveNueva', PasswordType::class, $propiedades)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $usuario = $form->get('codigoUsuarioPk')->getData();
                $arUsuario->setCodigoUsuarioPk($usuario);
                $arUsuario->setNombres($form->get('nombres')->getData());
                $arUsuario->setApellidos($form->get('apellidos')->getData());
                $arUsuario->setNumeroIdentificacion($form->get('numeroIdentificacion')->getData());
                $arUsuario->setIdentificacionRel($form->get('identificacionRel')->getData());
                $arUsuario->setCorreo($form->get('correo')->getData());
                $arUsuario->setCliente($form->get('cliente')->getData());
                $arUsuario->setProveedor($form->get('proveedor')->getData());
                $arUsuario->setGestionTranporte($form->get('gestionTranporte')->getData());
                $arUsuario->setGuiaNuevo($form->get('guiaNuevo')->getData());
                $arUsuario->setBloquearRecaudo($form->get('bloquearRecaudo')->getData());
                $arUsuario->setBloquearAdquirienteCredito($form->get('bloquearAdquirienteCredito')->getData());
                $arUsuario->setCambiarValoresGuia($form->get('cambiarValoresGuia')->getData());
                $arUsuario->setInvertirOrigenDestino($form->get('invertirOrigenDestino')->getData());
                $arUsuario->setCodigoTerceroErpFk($form->get('codigoTerceroErpFk')->getData());
                $arUsuario->setCodigoOperacionFk($form->get('codigoOperacionFk')->getData());
                $arUsuario->setCodigoOperacionClienteFk($form->get('codigoOperacionClienteFk')->getData());
                $arUsuario->setCodigoCiudadOrigenFk($form->get('codigoCiudadOrigenFk')->getData());
                $arUsuario->setPermiteCambiarAdquiriente($form->get('permiteCambiarAdquiriente')->getData());
                $arUsuario->setEstadoRecogido($form->get('estadoRecogido')->getData());
                $arUsuario->setEstadoIngreso($form->get('estadoIngreso')->getData());
                $arUsuario->setDireccionRemitente($form->get('direccionRemitente')->getData());
                $arUsuario->setTelefonoRemitente($form->get('telefonoRemitente')->getData());
                if ($id == "0") {
                    $arUsuario->setFechaRegistro(new \DateTime('now'));
                    $arUsuario->setEmpresaRel($arEmpresa);
                    $arUsuario->setEmpresa(false);
                    $arUsuario->setEmpleado(false);
                    $arUsuario->setCodigoRolFk("ROLE_USER");
                    $arUsuario->setClave($form->get('claveNueva')->getData());
                    $arUsuarioExistente = $em->getRepository(Usuario::class)->find($form->get('codigoUsuarioPk')->getData());
                    if ($arUsuarioExistente) {
                        Mensajes::error("Ya existe un usuario con el nombre de usuario '{$form->get('codigoUsuarioPk')->getData()}'");
                    } else {
                        $em->persist($arUsuario);
                        $em->flush();
                        Mensajes::success("Registro creado");
                        return $this->redirect($this->generateUrl('empresa_usuario_detalle', ['id' => $arUsuario->getCodigoUsuarioPk()]));
                    }
                } else {
                    $em->persist($arUsuario);
                    $em->flush();
                    Mensajes::success("Registro actualizado");
                    return $this->redirect($this->generateUrl('empresa_usuario_detalle', ['id' => $arUsuario->getCodigoUsuarioPk()]));
                }
            }
        }
        return $this->render('aplicacion/empresa/usuario/nuevo.html.twig', [
            'arUsuario' => $arUsuario,
            'id' => $id,
            'ingresarContrasena' => $ingresarContrasena,
            'form' => $form->createView()
        ]);
    }

    #[Route("/empresa/usuario/detalle/{id}", name:"empresa_usuario_detalle")]
    public function detalle(Request $request, PaginatorInterface $paginator, $id,  EntityManagerInterface $em)
    {
        $arRegistro = $em->getRepository(Usuario::class)->find($id);
        return $this->render('aplicacion/empresa/usuario/detalle.html.twig', [
            'arRegistro' => $arRegistro,
        ]);
    }

    #[Route("/empresa/usuario/cambioclave/{id}", name:"empresa_usuario_cambioclave")]
    public function cambiarClave(Request $request, $id,  EntityManagerInterface $em)
    {
        $respuesta = '';
        if ($id != 0) {
            $arUsuario = $em->getRepository(Usuario::class)->find($id);
            if (!$arUsuario) {
                return $this->redirect($this->generateUrl('empresa_usuario_lista'));
            }
        }
        $form = $this->createFormBuilder()
            ->add('txtNuevaClave', PasswordType::class, ['required' => true])
            ->add('txtConfirmacionClave', PasswordType::class, ['required' => true])
            ->add('btnActualizar', SubmitType::class, ['label' => 'Actualizar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        $arUsuario = $em->getRepository(Usuario::class)->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnActualizar')->isClicked()) {
                $claveNueva = $form->get('txtNuevaClave')->getData();
                $claveConfirmacion = $form->get('txtConfirmacionClave')->getData();
                if ($claveNueva == $claveConfirmacion) {
                    $arUsuario->setClave($claveNueva);
                    $em->persist($arUsuario);
                } else {
                    $respuesta = "Las claves ingresadas no coinciden";
                }
            }
            if ($respuesta != '') {
                Mensajes::error($respuesta);
            } else {
                $em->flush();
                echo "<script languaje='javascript' type='text/javascript'>window.close();window.opener.location.reload();</script>";
            }
        }
        return $this->render('aplicacion/empresa/usuario/cambioContrasena.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function filtros($form)
    {
        $session = new Session();
        $filtro = [
            "rol" => $form->get('codigoRolFk')->getData(),
            "numeroIdentificacion" => $form->get('numeroIdentificacion')->getData(),
            "nombre" => $form->get('nombre')->getData(),
            "codigoUsuario" => $form->get('codigoUsuario')->getData()
        ];
        $session->set('filtroUsuarios.', $filtro);
        return $filtro;
    }

    public function excelLista($arRegistros, EntityManagerInterface $em)
    {
        set_time_limit(0);
        ini_set("memory_limit", -1);
        if ($arRegistros) {
            $libro = new Spreadsheet();
            $hoja = $libro->getActiveSheet();
            $hoja->getStyle(1)->getFont()->setName('Arial')->setSize(8);
            $hoja->setTitle('Usuarios');
            $j = 0;
            $arrColumnas = ['USUARIO', 'TI', 'NI', 'NOMBRE', 'APELLIDO', 'CORREO', 'F_REGISTRO', 'ERP', 'OPERACIÓN', 'EMPLEADO', 'CLIENTE', 'PROVEEDOR'];
            for ($i = 'A'; $j <= sizeof($arrColumnas) - 1; $i++) {
                $hoja->getColumnDimension($i)->setAutoSize(true);
                $hoja->getStyle(1)->getFont()->setName('Arial')->setSize(8);
                $hoja->getStyle(1)->getFont()->setBold(true);
                $hoja->setCellValue($i . '1', strtoupper($arrColumnas[$j]));
                $j++;
            }
            $j = 2;
            foreach ($arRegistros as $arRegistro) {
                $hoja->getStyle($j)->getFont()->setName('Arial')->setSize(8);
                $hoja->getStyle("G{$j}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

                $hoja->setCellValue('A' . $j, $arRegistro['codigoUsuarioPk']);
                $hoja->setCellValue('B' . $j, $arRegistro['codigoIdentificacionFk']);
                $hoja->setCellValue('C' . $j, $arRegistro['numeroIdentificacion']);
                $hoja->setCellValue('D' . $j, $arRegistro['nombres']);
                $hoja->setCellValue('E' . $j, $arRegistro['apellidos']);
                $hoja->setCellValue('F' . $j, $arRegistro['correo']);
                $hoja->setCellValue('G' . $j, $arRegistro['fechaRegistro'] ? Date::PHPToExcel($arRegistro['fechaRegistro']->format("Y-m-d")) : null);
                $hoja->setCellValue('H' . $j, $arRegistro['codigoTerceroErpFk']);
                $hoja->setCellValue('I' . $j, $arRegistro['codigoOperacionFk']);
                $hoja->setCellValue('J' . $j, $arRegistro['empleado'] ? 'SI' : 'NO');
                $hoja->setCellValue('K' . $j, $arRegistro['cliente'] ? 'SI' : 'NO');
                $hoja->setCellValue('L' . $j, $arRegistro['proveedor'] ? 'SI' : 'NO');
                $j++;
            }
            /*$hoja2 = new Worksheet($libro, "Empleados");
            $libro->addSheet($hoja2);
            $j = 0;
            $arrColumnas = ['CÓDIGO EMPLEADO', 'EMPLEADO', 'CÉDULA', 'CORREO', 'CELULAR', 'CARGO', 'ACTIVO', 'ZONA', 'SUBZONA'];
            for ($i = 'A'; $j <= sizeof($arrColumnas) - 1; $i++) {
                $hoja2->getColumnDimension($i)->setAutoSize(true);
                $hoja2->getStyle(1)->getFont()->setName('Arial')->setSize(8);
                $hoja2->getStyle(1)->getFont()->setBold(true);
                $hoja2->setCellValue($i . '1', strtoupper($arrColumnas[$j]));
                $j++;
            }
            $j = 2;
            foreach ($arrEmpleados as $arrEmpleado) {
                $arUsuarios = $em->getRepository(Usuario::class)->findBy(['codigoIdentificacionFk' => $arrEmpleado['codigoIdentificacionFk'], 'numeroIdentificacion' => $arrEmpleado['numeroIdentificacion']]);
                foreach ($arUsuarios as $arUsuario) {
                    $codigoEmpleado = $arrEmpleado['codigoEmpleadoPk'];
                    $empleado = $arrEmpleado['nombreCorto'];
                    $cedula = $arrEmpleado['numeroIdentificacion'];
                    $correo = $arrEmpleado['correo'];
                    $celular = $arrEmpleado['celular'];
                    $activo = $arrEmpleado['estadoContrato'];
                    $subzona = $arrEmpleado['subzonaNombre'];
                    $zona = $arrEmpleado['zonaNombre'];
                    if ($activo == 'SI') {
                        $cargo = $arrEmpleado['cargoNombre'];
                    } else {
                        $cargo = $arrEmpleado['cargoNombreContratoUltimo'];
                    }
                    $hoja2->getStyle($j)->getFont()->setName('Arial')->setSize(8);
                    $hoja2->setCellValue('A' . $j, $codigoEmpleado);
                    $hoja2->setCellValue('B' . $j, $empleado);
                    $hoja2->setCellValue('C' . $j, $cedula);
                    $hoja2->setCellValue('D' . $j, $correo);
                    $hoja2->setCellValue('E' . $j, $celular);
                    $hoja2->setCellValue('F' . $j, $cargo);
                    $hoja2->setCellValue('G' . $j, $activo ? 'SI' : 'NO');
                    $hoja2->setCellValue('H' . $j, $subzona);
                    $hoja2->setCellValue('I' . $j, $zona);
                    $j++;
                }
            }*/
            $libro->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=usuarios.xls");
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($libro, 'Xls');
            $writer->save('php://output');
        } else {
            Mensajes::error("No existen registros para exportar");
        }
    }
}