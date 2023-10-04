<?php


namespace App\Entity;

use App\Repository\TextoRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: TextoRepository::class)]
class Texto
{

    #[ORM\Column(type: "integer", name: "codigo_texto_pk")]
    #[ORM\Id]
    private $codigoTextoPk;


    /**
     * @ORM\Column(name="texto", type="text", nullable=true)
     */
    #[ORM\Column(type: "text", name: "texto", nullable: true)]
    private $texto;

    #[ORM\Column(type: "integer", name: "codigo_empresa_fk", nullable: true)]
    private $codigoEmpresaFk;

    /**
     * @ORM\Column(name="codigo_texto_tipo_fk", type="string", length=10, nullable=true)
     */
    #[ORM\Column(type: "string", name: "codigo_texto_tipo_fk", length: 10, nullable: true)]
    private $codigoTextoTipoFk;


    #[ORM\ManyToOne(targetEntity: Empresa::class, inversedBy: "textosEmpresaRel")]
    #[ORM\JoinColumn(name: "codigo_empresa_fk", referencedColumnName: "codigo_empresa_pk")]
    protected $empresaRel;


    #[ORM\ManyToOne(targetEntity: TextoTipo::class, inversedBy: "textosTextoTipoRel")]
    #[ORM\JoinColumn(name: "codigo_texto_tipo_fk", referencedColumnName: "codigo_texto_tipo_pk")]
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