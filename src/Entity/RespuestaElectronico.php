<?php


namespace App\Entity;

use App\Repository\RespuestaElectronicaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RespuestaElectronicaRepository::class)]
class RespuestaElectronico
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"AUTO")]
    #[ORM\Column(type:"integer", name:"codigo_respuesta_electronico_pk", unique:true)]
    private $codigoRespuestaElectronicoPk;


    #[ORM\Column(name:"codigo_empresa_fk", type:"integer", nullable: false)]
    private $codigoEmpresaFk;


    #[ORM\Column(name:"codigo_factura_fk", type:"integer", nullable: false)]
    private $codigoFacturaFk;


    #[ORM\Column(name: "modelo", type: "string", length: 100, nullable: false)]
    private $modelo;


    #[ORM\Column(name:"fecha_respuesta_electronico", type:"datetime", nullable: true)]
    private $fechaRespuestaElectronico;


    #[ORM\Column(name: "ip", type: "string", length: 300, nullable: true)]
    #[Assert\Length(max: 300, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $ip;


    #[ORM\Column(name: "respuesta_electronico", type: "string", length: 3, nullable: true, options: ["default" => "P"])]
    private $respuestaElectronico = 'P';

    /**
     * @return mixed
     */
    public function getCodigoRespuestaElectronicoPk()
    {
        return $this->codigoRespuestaElectronicoPk;
    }

    /**
     * @param mixed $codigoRespuestaElectronicoPk
     */
    public function setCodigoRespuestaElectronicoPk($codigoRespuestaElectronicoPk): void
    {
        $this->codigoRespuestaElectronicoPk = $codigoRespuestaElectronicoPk;
    }

    /**
     * @return mixed
     */
    public function getCodigoEmpresaFk()
    {
        return $this->codigoEmpresaFk;
    }

    /**
     * @param mixed $codigoEmpresaFk
     */
    public function setCodigoEmpresaFk($codigoEmpresaFk): void
    {
        $this->codigoEmpresaFk = $codigoEmpresaFk;
    }

    /**
     * @return mixed
     */
    public function getCodigoFacturaFk()
    {
        return $this->codigoFacturaFk;
    }

    /**
     * @param mixed $codigoFacturaFk
     */
    public function setCodigoFacturaFk($codigoFacturaFk): void
    {
        $this->codigoFacturaFk = $codigoFacturaFk;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getRespuestaElectronico(): string
    {
        return $this->respuestaElectronico;
    }

    /**
     * @param string $respuestaElectronico
     */
    public function setRespuestaElectronico(string $respuestaElectronico): void
    {
        $this->respuestaElectronico = $respuestaElectronico;
    }

    /**
     * @return mixed
     */
    public function getFechaRespuestaElectronico()
    {
        return $this->fechaRespuestaElectronico;
    }

    /**
     * @param mixed $fechaRespuestaElectronico
     */
    public function setFechaRespuestaElectronico($fechaRespuestaElectronico): void
    {
        $this->fechaRespuestaElectronico = $fechaRespuestaElectronico;
    }

    /**
     * @return mixed
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * @param mixed $modelo
     */
    public function setModelo($modelo): void
    {
        $this->modelo = $modelo;
    }


}