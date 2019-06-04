<?php

namespace App\Controller;


use FOS\RestBundle\FOSRestBundle;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiServicioController extends FOSRestBundle
{

    /**
     * @return array
     * @Rest\Post("/api/servicio/mensaje")
     */
    public function mensaje(Request $request) {
        try {
            return [
                'mensaje' => "hola mundo ",
            ];
//            $em = $this->getDoctrine()->getManager();
//            $raw = json_decode($request->getContent(), true);
//            return $em->getRepository(TteGuia::class)->apiWindowsDetalle($raw);
        } catch (\Exception $e) {
            return [
                'error' => "Ocurrio un error en la api " . $e->getMessage(),
            ];
        }
    }


}
