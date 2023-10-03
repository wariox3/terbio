<?php


namespace App\Controller\Aplicacion\Cliente;


use App\Entity\Enlace;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{
    /**
     * @Route("/cliente", name="cliente_inicio")
     */
    public function inicioAction(Request $request)
    {
        return $this->render('aplicacion/inicio.html.twig');
    }
}