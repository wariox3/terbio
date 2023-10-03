<?php

namespace App\twig;

use App\Entity\Configuracion;
use App\Entity\Enlace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $em;
    private $arUsuario;

    public function getFunctions(): ?array
    {
        return [
            new TwigFunction('notificar', [$this, 'getNotifies']),
            new TwigFunction('diaProgramacion', [$this, 'diaProgramacion']),
            new TwigFunction('mesLetras', [$this, 'mesLetras']),
            new TwigFunction('enlaces', [$this, 'enlaces']),
            new TwigFunction('obtenerLogo', [$this, 'obtenerLogo']),
        ];
    }

    /**
     * AppExtension constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->arUsuario = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;
    }

    /**
     * Esta función nos permite obtener código html sin violar estandares de mezcla de código.
     * @param $tag
     * @param string $content
     * @param array $attrs
     * @return string
     */
    private function createTag($tag, $content = '', $attrs = [])
    {
        $attrs = implode(" ", array_map(function ($attr, $value) {
            return "{$attr}=\"{$value}\"";
        }, array_keys($attrs), $attrs));
        return "<{$tag}" . ($attrs ? " {$attrs}" : "") . ">{$content}</{$tag}>";
    }

    /**
     * Esta función se encarga de imprimir las notificaciones para usuario (Mensajes).
     * @return string
     */
    public function getNotifies()
    {
        $session = new Session();
        $flashes = $session->getFlashBag()->all();
        $html = [];
        foreach ($flashes as $type => $messages) {
            foreach ($messages as $message) {
                $span = $this->createTag("span", "&times;", ['aria-hidden' => 'true']);
                $button = $this->createTag("button", $span, ['class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']);
                $alert = $this->createTag("div", $button . $message, ['class' => "alert alert-{$type}", 'data', 'style' => 'margin-top:5px;margin-bottom:5px;']);
                $html[] = $alert;
            }
        }
        $session->getFlashBag()->clear();
        return implode('', $html);
    }

    public function diaProgramacion($arProgramacion, $dia)
    {
        $dia = "dia{$dia}";
        return array('dia' => $arProgramacion->$dia);
    }

    public function mesLetras($mes)
    {
        $letras = "";
        switch ($mes) {
            case '1';
                $letras = "Enero";
                break;

            case '1';
                $letras = "Enero";
                break;

            case '2';
                $letras = "Febrero";
                break;

            case '3';
                $letras = "Marzo";
                break;

            case '4';
                $letras = "Abril";
                break;

            case '5';
                $letras = "Mayo";
                break;

            case '6';
                $letras = "Junio";
                break;

            case '7';
                $letras = "Julio";
                break;

            case '8';
                $letras = "Agosto";
                break;

            case '9';
                $letras = "Septiembre";
                break;

            case '10';
                $letras = "Octubre";
                break;

            case '11';
                $letras = "Noviembre";
                break;

            case '12';
                $letras = "Diciembre";
                break;
        }
        return $letras;
    }

    public function enlaces()
    {
        $arEnlaces = null;
        if ($this->arUsuario->getCodigoEmpresaFk()) {
            $arEnlaces = $this->em->getRepository(Enlace::class)->lista($this->arUsuario->getCodigoEmpresaFk());
        }
        return $arEnlaces;
    }

    public function obtenerLogo()
    {
        $arConfiguracion = $this->em->getRepository(Configuracion::class)->obtenerLogo();
        $images = array();
        foreach ($arConfiguracion as $key => $entity) {
            $images[$key] = base64_encode(stream_get_contents($entity));
        }

        return $images;
    }

}