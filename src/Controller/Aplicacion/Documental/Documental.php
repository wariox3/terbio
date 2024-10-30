<?php


namespace App\Controller\Aplicacion\Documental;


use App\Entity\Archivo;
use App\Entity\Directorio;
use App\Entity\Imagen;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class Documental  extends AbstractController
{
    #[Route("/documental/general/lista/{tipo}/{codigo}", name:"documental_general_general_lista")]
    public function listaAction(Request $request, $tipo, $codigo, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $query = $em->getRepository(Archivo::class)->listaArchivo($tipo, $codigo);
        $arArchivos = $paginator->paginate($query, $request->query->get('page', 1), 50);
        $arImagen = $em->getRepository(Imagen::class)->findOneBy(array('codigoModeloFk' => $tipo, 'identificador' => $codigo));
        $srcImagen = "";
        if ($arImagen) {
            $strFichero =  "/almacenamiento/oxigeno/imagen/" . $arImagen->getCodigoModeloFk() . "/" . $arImagen->getDirectorio() . "/" . $arImagen->getCodigoImagenPk() . "_" . $arImagen->getNombre();
            if (file_exists($strFichero)) {
                $base64 = base64_encode(file_get_contents($strFichero));
                $tipoArchivo = $arImagen->getTipo();
                $srcImagen = "data: {$tipoArchivo}; base64, {$base64}";
            }
        }
        return $this->render('aplicacion/documental/general/lista.html.twig', array(
            'arArchivos' => $arArchivos,
            'tipo' => $tipo,
            'codigo' => $codigo,
            'srcImagen' => $srcImagen
        ));
    }

    #[Route("/documental/general/cargar/{tipo}/{codigo}", name:"documental_general_general_cargar")]
    public function cargarAction(Request $request, $tipo, $codigo,  EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->add('attachment', FileType::class)
            ->add('descripcion', TextType::class, array('required' => false))
            ->add('BtnCargar', SubmitType::class, array('label' => 'Cargar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('BtnCargar')->isClicked()) {
                $objArchivo = $form['attachment']->getData();
                if ($objArchivo->getSize()) {
                    $peso = $objArchivo->getSize() / 1000000;
                    if($peso <= 6) {
                        $arArchivo = new Archivo();
                        $arArchivo->setNombre($objArchivo->getClientOriginalName());
                        $arArchivo->setExtensionOriginal($objArchivo->getClientOriginalExtension());
                        $arArchivo->setTamano($objArchivo->getSize());
                        $arArchivo->setTipo($objArchivo->getClientMimeType());
                        $arArchivo->setCodigoArchivoTipoFk($tipo);
                        $arArchivo->setCodigo($codigo);
                        $arArchivo->setUsuario($this->getUser()->getUsername());
                        $dateFecha = new \DateTime('now');
                        $arArchivo->setFecha($dateFecha);
                        $arArchivo->setDescripcion($form->get('descripcion')->getData());
                        $directorio = $em->getRepository(Directorio::class)->devolverDirectorio("A", $tipo);
                        $arArchivo->setDirectorio($directorio);
                        $arArchivo->setCodigoArchivoTipoFk($tipo);
                        $error = false;
                        $directorioDestino = "/almacenamiento/oxigeno/archivo/" . $tipo . "/" . $directorio . "/";
                        if (!file_exists($directorioDestino)) {
                            if (!mkdir($directorioDestino, 0777, true)) {
                                Mensajes::error('Fallo al crear directorio...' . $directorioDestino);
                                $error = true;
                            }
                        }
                        if ($error == false) {
                            $em->persist($arArchivo);
                            $em->flush();
                            $strArchivo = $arArchivo->getCodigoArchivoPk() . "_" . $objArchivo->getClientOriginalName();
                            $form['attachment']->getData()->move($directorioDestino, $strArchivo);
                        }
                        return $this->redirect($this->generateUrl('documental_general_general_lista', array('tipo' => $tipo, 'codigo' => $codigo)));
                    } else {
                        Mensajes::error("El archivo tiene un tamaño mayor al permitido");
                    }
                } else {
                    Mensajes::error("El archivo tiene un tamaño mayor al permitido");
                }
            }
        }
        return $this->render('aplicacion/documental/general/cargar.html.twig', array(
            'form' => $form->createView(),
            'tipo' => $tipo,
            'codigo' => $codigo
        ));
    }

    #[Route("/documental/general/descargar/{codigoArchivo}", name:"documental_general_general_descargar")]
    public function descargarAction($codigoArchivo,  EntityManagerInterface $em)
    {
        $arArchivo = $em->getRepository(Archivo::class)->find($codigoArchivo);
        $strRuta =  "/almacenamiento/oxigeno/archivo/" . $arArchivo->getCodigoArchivoTipoFk() . "/" . $arArchivo->getDirectorio() . "/" . $arArchivo->getCodigoArchivoPk() . "_" . $arArchivo->getNombre();
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', $arArchivo->getTipo());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $arArchivo->getNombre() . '";');
        $response->headers->set('Content-length', $arArchivo->getTamano());
        $response->sendHeaders();
        if (file_exists($strRuta)) {
            $response->setContent(readfile($strRuta));
        } else {
            echo "<script>alert('No existe el archivo en el servidor a pesar de estar asociado en base de datos, por favor comuniquese con soporte');window.close()</script>";
        }
        return $response;

    }

    #[Route("/documental/general/cargar/imagen/{modelo}/{codigo}", name:"documental_general_general_cargar_imagen")]
    public function cargarImagenAction(Request $request, $modelo, $codigo,  EntityManagerInterface $em)
    {
        $arImagen = $em->getRepository(Imagen::class)->findOneBy(array('codigoModeloFk' => $modelo, 'identificador' => $codigo));
        $form = $this->createFormBuilder()
            ->add('attachment', FileType::class)
            ->add('btnCargar', SubmitType::class, array('label' => 'Cargar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnCargar')->isClicked()) {
                $objArchivo = $form['attachment']->getData();
                if ($objArchivo->getSize()) {
                    $directorio = $em->getRepository(Directorio::class)->devolverDirectorio("I", $modelo);
                    $directorioDestino =  "/almacenamiento/oxigeno/imagen/" . $modelo . "/" . $directorio . "/";
                    if (!file_exists($directorioDestino)) {
                        if (!mkdir($directorioDestino, 0777, true)) {
                            Mensajes::error('Fallo al crear directorio...' . $directorioDestino);
                            $error = true;
                        }
                    }
                    if ($arImagen) {
                        $strFichero = "/almacenamiento/oxigeno/imagen/" . $arImagen->getCodigoModeloFk() . "/" . $arImagen->getDirectorio() . "/" . $arImagen->getCodigoImagenPk() . "_" . $arImagen->getNombre();
                        unlink($strFichero);
                        $arImagen = $em->getRepository(Imagen::class)->find($arImagen->getCodigoImagenPk());
                    } else {
                        $arImagen = new Imagen();
                    }
                    $arImagen->setNombre($objArchivo->getClientOriginalName());
                    $arImagen->setExtensionOriginal($objArchivo->getClientOriginalExtension());
                    $arImagen->setTamano($objArchivo->getSize());
                    $arImagen->setTipo($objArchivo->getClientMimeType());
                    $arImagen->setCodigoModeloFk($modelo);
                    $arImagen->setIdentificador($codigo);
                    $dateFecha = new \DateTime('now');
                    $arImagen->setFecha($dateFecha);
                    $arImagen->setDirectorio($directorio);

                    $error = false;
                    if ($error == false) {
                        $em->persist($arImagen);
                        $em->flush();
                        $strArchivo = $arImagen->getCodigoImagenPk() . "_" . $objArchivo->getClientOriginalName();
                        $form['attachment']->getData()->move($directorioDestino, $strArchivo);
                    }
                    return $this->redirect($this->generateUrl('documental_general_general_lista', array('tipo' => $modelo, 'codigo' => $codigo)));
                } else {
                    Mensajes::error("El archivo tiene un tamaño mayor al permitido");
                }
            }
        }
        return $this->render('aplicacion/documental/general/cargarImagen.html.twig', array(
            'form' => $form->createView(),
            'tipo' => $modelo,
            'codigo' => $codigo
        ));
    }

    #[Route("/documental/general/eliminar/{tipo}/{codigoMovimiento}/{codigoArchivo}", name:"documental_general_general_eliminar")]
    public function EliminarAction($tipo, $codigoMovimiento, $codigoArchivo,  EntityManagerInterface $em)
    {
        $arArchivo = $em->getRepository(Archivo::class)->find($codigoArchivo);
        if (!$arArchivo) {
            return $this->redirect($this->generateUrl('documental_general_general_lista', array('tipo' => $tipo, 'codigo' => $codigoMovimiento)));
        }
        $strRuta = "/almacenamiento/oxigeno/archivo/" . $arArchivo->getCodigoArchivoTipoFk() . "/" . $arArchivo->getDirectorio() . "/" . $arArchivo->getCodigoArchivoPk() . "_" . $arArchivo->getNombre();
        if (file_exists($strRuta)) {
            unlink($strRuta);
        }
        $em->remove($arArchivo);
        $em->flush();
        return $this->redirect($this->generateUrl('documental_general_general_lista', array('tipo' => $tipo, 'codigo' => $codigoMovimiento)));
    }
}