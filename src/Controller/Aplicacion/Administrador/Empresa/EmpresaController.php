<?php


namespace App\Controller\Aplicacion\Administrador\Empresa;


use App\Entity\Empresa;
use App\Form\Type\Empresa\EmpresaType;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class EmpresaController extends AbstractController
{
    #[Route("/administrador/empresa/lista", name:"administrador_empresa_lista")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $session = new Session();
        $form = $this->createFormBuilder()
            ->add('codigo', TextType::class, array('required' => false, 'data'=>""))
            ->add('nombre', TextType::class, array('required' => false, 'data'=>""))
            ->add('identificacion', TextType::class, array('required' => false, 'data'=>""))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->get('btnFiltro')->isClicked()) {
                $session->set('filtroEmpresaCodigo', $form->get('codigo')->getData());
                $session->set('filtroEmpresaNombre', $form->get('nombre')->getData());
                $session->set('filtroEmpresaNumeroIdentificacion', $form->get('identificacion')->getData());
            }
        }
        $arEmpresas = $paginator->paginate($em->getRepository(Empresa::class)->lista(), $request->query->getInt('page', 1), 30);
        return $this->render('aplicacion/empresa/empresa/lista.html.twig', [
            'arEmpresas' => $arEmpresas,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/administrador/empresa/nuevo/{id}", name:"administrador_empresa_nuevo")]
    public function nuevo(Request $request, $id,  EntityManagerInterface $em)
    {
        $arRegistro = new Empresa;
        if ($id != 0) {
            $arRegistro = $em->getRepository(Empresa::class)->find($id);
        }

        $formatosValidos= ["jpeg", "jpg", "png"];
        $form = $this->createFormBuilder()
            ->add ('nombre', TextType::class, ['required' => true, 'data'=>$arRegistro->getNombre(), 'attr'=>['class'=>'form-control']])
            ->add ('direccion', TextType::class, ['required' => true,'data'=>$arRegistro->getDireccion(),  'attr'=>['class'=>'form-control']])
            ->add ('telefono', TextType::class, ['required' => true,'data'=>$arRegistro->getTelefono(),  'attr'=>['class'=>'form-control']])
            ->add ('nit', TextType::class, ['required' => true,'data'=>$arRegistro->getNit(),  'attr'=>['class'=>'form-control']])
            ->add ('digitoVerificacion', TextType::class, ['required' => true,'data'=>$arRegistro->getDigitoVerificacion(),  'attr'=>['class'=>'form-control']])
            ->add ('abreviatura', TextType::class, ['required' => true,'data'=>$arRegistro->getAbreviatura(),  'attr'=>['class'=>'form-control']])
            ->add ('urlServicio', UrlType::class, ['required' => true, 'data'=>$arRegistro->getUrlServicio(), 'attr'=>['class'=>'form-control']])
            ->add ('ciudad', TextType::class, ['required' => true, 'data'=>$arRegistro->getCiudad(), 'attr'=>['class'=>'form-control']])
            ->add ('codigoItem', TextType::class, ['required' => true, 'data'=>$arRegistro->getCodigoItem(), 'attr'=>['class'=>'form-control']])
            ->add('archivo',fileType::class, array('required' => false))
            ->add('btnGuardar', SubmitType::class, ['label'=>'Guardar','attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $objArchivo = $form['archivo']->getData();
                $arRegistro->setNombre($form['nombre']->getData());
                $arRegistro->setDireccion($form['direccion']->getData());
                $arRegistro->setTelefono($form['telefono']->getData());
                $arRegistro->setNit($form['nit']->getData());
                $arRegistro->setDigitoVerificacion($form['digitoVerificacion']->getData());
                $arRegistro->setAbreviatura($form['abreviatura']->getData());
                $arRegistro->setUrlServicio($form['urlServicio']->getData());
                $arRegistro->setCiudad($form['ciudad']->getData());
                $arRegistro->setCodigoItem($form['codigoItem']->getData());
                if(! is_null($objArchivo)) {
                    if ($objArchivo->getClientSize()) {
                        $peso = $objArchivo->getClientSize() / 1000000;
                        $strm = fopen($objArchivo->getRealPath(),'rb');
                        if($peso <= 6) {
                            if(in_array($objArchivo->getClientOriginalExtension(), $formatosValidos)){
                                $arRegistro->setLogo(stream_get_contents($strm));
                                $arRegistro->setExtension($objArchivo->getClientOriginalExtension());
                                echo "<script languaje='javascript' type='text/javascript'>window.close();window.opener.location.reload();</script>";
                            }else{
                                Mensajes::warning("El archivo tiene no tiene el formato valido");
                            }
                        }else{
                            Mensajes::error("El archivo tiene un tamaño mayor al permitido");
                        }
                    } else {
                        Mensajes::error("El archivo tiene un tamaño mayor al permitido");
                    }
                }
                $em->persist($arRegistro);
                $em->flush();
                return $this->redirect($this->generateUrl('administrador_empresa_detalle', array('id' => $arRegistro->getCodigoEmpresaPk())));

            }
        }
        return $this->render('aplicacion/empresa/empresa/nuevo.html.twig', [
            'form' => $form->createView(),
            'arRegistro' => $arRegistro
        ]);
    }

    #[Route("/administrador/empresa/detalle/{id}", name:"administrador_empresa_detalle")]
    public function detalle(Request $request, $id,  EntityManagerInterface $em)
    {
        $logo= null;
        $arRegistro = $em->getRepository(Empresa::class)->find($id);
        if ($arRegistro->getLogo()){
            $logo = base64_encode(stream_get_contents($arRegistro->getLogo()));
        }

        return $this->render('aplicacion/empresa/empresa/detalle.html.twig', [
            'arRegistro' => $arRegistro,
            'logo'=>$logo
        ]);
    }
}