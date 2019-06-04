<?php

namespace App\Controller;

use App\Entity\Movimiento;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class InicioController extends AbstractController
{
   /**
    * @Route("/", name="inicio")
    */    
    public function inicio(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('inicio.html.twig');
    }
}

