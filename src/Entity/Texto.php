<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TextoRepository")
 */
class Texto
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_texto_pk", type="integer")
     */
    private $codigoTextoPk;


    /**
     * @ORM\Column(name="texto", type="text", nullable=true)
     */
    private $texto;

    /**
     * @ORM\Column(name="codigo_empresa_fk", type="integer", nullable=true)
     */
    private $codigoEmpresaFk;

    /**
     * @ORM\Column(name="codigo_texto_tipo_fk", type="string", length=10, nullable=true)
     */
    private $codigoTextoTipoFk;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Empresa", inversedBy="textosEmpresaRel")
     * @ORM\JoinColumn(name="codigo_empresa_fk",referencedColumnName="codigo_empresa_pk")
     */
    protected $empresaRel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TextoTipo", inversedBy="textosTextoTipoRel")
     * @ORM\JoinColumn(name="codigo_texto_tipo_fk",referencedColumnName="codigo_texto_tipo_pk")
     */
    protected $textoTipoRel;

    /**
     * @return mixed
     */
    public function getCodigoTextoPk()
    {
        return $this->codigoTextoPk;
    }

    /**
     * @param mixed $codigoTextoPk
     */
    public function setCodigoTextoPk($codigoTextoPk): void
    {
        $this->codigoTextoPk = $codigoTextoPk;
    }

    /**
     * @return mixed
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * @param mixed $texto
     */
    public function setTexto($texto): void
    {
        $this->texto = $texto;
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
    public function getEmpresaRel()
    {
        return $this->empresaRel;
    }

    /**
     * @param mixed $empresaRel
     */
    public function setEmpresaRel($empresaRel): void
    {
        $this->empresaRel = $empresaRel;
    }

    /**
     * @return mixed
     */
    public function getCodigoTextoTipoFk()
    {
        return $this->codigoTextoTipoFk;
    }

    /**
     * @param mixed $codigoTextoTipoFk
     */
    public function setCodigoTextoTipoFk($codigoTextoTipoFk): void
    {
        $this->codigoTextoTipoFk = $codigoTextoTipoFk;
    }

    /**
     * @return mixed
     */
    public function getTextoTipoRel()
    {
        return $this->textoTipoRel;
    }

    /**
     * @param mixed $textoTipoRel
     */
    public function setTextoTipoRel($textoTipoRel): void
    {
        $this->textoTipoRel = $textoTipoRel;
    }


}