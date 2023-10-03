<?php


namespace App\Repository;

use App\Entity\RespuestaElectronico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RespuestaElectronicaRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RespuestaElectronico::class);
    }

    public function apiConsultaEmpresa($codigoEmpresa, $modelo, $codigoFactura)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->from(RespuestaElectronico::class, 're')
            ->select('re.codigoRespuestaElectronicoPk')
            ->addSelect('re.ip')
            ->addSelect('re.fechaRespuestaElectronico')
            ->addSelect('re.respuestaElectronico')
            ->where("re.codigoEmpresaFk = {$codigoEmpresa}")
            ->andWhere("re.modelo = '{$modelo}'")
            ->andWhere("re.codigoFacturaFk = {$codigoFactura}");
        $arRespuestas = $queryBuilder->getQuery()->getResult();
        return [
            'error' => false,
            'respuestaElectronico' => $arRespuestas
        ];
    }
}