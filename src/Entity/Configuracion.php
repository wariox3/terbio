<?php


namespace App\Entity;

use App\Repository\ConfiguracionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ConfiguracionRepository::class)]
class Configuracion
{

    #[ORM\Id]
    #[ORM\Column(name: "username", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $codigoConfiguracionPk;

    /**
     * @ORM\Column(name="logo", type="blob", nullable=true)
     */
    #[ORM\Column(name: "logo", type: "blob", nullable: true)]
    private $logo;


    #[ORM\Column(name: "telefono_soporte", type: "string", length: 25, nullable: true)]
    #[Assert\Length(max: 25, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $telefonoSoporte;


    #[ORM\Column(name: "codigo_empresa_principal", type: "string", length: 25, nullable: true)]
    #[Assert\Length(max: 25, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $codigoEmpresaPrincipal;

    /**
     * @return mixed
     */
    public function getCodigoConfiguracionPk()
    {
        return $this->codigoConfiguracionPk;
    }

    /**
     * @param mixed $codigoConfiguracionPk
     */
    public function setCodigoConfiguracionPk($codigoConfiguracionPk): void
    {
        $this->codigoConfiguracionPk = $codigoConfiguracionPk;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getTelefonoSoporte()
    {
        return $this->telefonoSoporte;
    }

    /**
     * @param mixed $telefonoSoporte
     */
    public function setTelefonoSoporte($telefonoSoporte): void
    {
        $this->telefonoSoporte = $telefonoSoporte;
    }

    /**
     * @return mixed
     */
    public function getCodigoEmpresaPrincipal()
    {
        return $this->codigoEmpresaPrincipal;
    }

    /**
     * @param mixed $codigoEmpresaPrincipal
     */
    public function setCodigoEmpresaPrincipal($codigoEmpresaPrincipal): void
    {
        $this->codigoEmpresaPrincipal = $codigoEmpresaPrincipal;
    }
}