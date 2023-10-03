<?php


namespace App\Repository;


use App\Entity\Texto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TextoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Texto::class);
    }

    public function lista($raw, $usuario)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Texto::class, 't')
            ->select('t.codigoTextoPk')
            ->addSelect('t.texto')
            ->addSelect('tt.descripcion')
            ->leftJoin('t.textoTipoRel', 'tt')
            ->where("t.codigoEmpresaFk = {$usuario->getCodigoEmpresaFk()}");
        $queryBuilder->addOrderBy('t.codigoTextoPk', 'DESC');
        return $queryBuilder->getQuery()->getResult();

    }
}