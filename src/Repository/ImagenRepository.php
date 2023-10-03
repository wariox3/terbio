<?php


namespace App\Repository;


use App\Entity\Imagen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

class ImagenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Imagen::class);
    }

    public function lista()
    {
        $session = new Session();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Imagen::class, 'm')
            ->select('m.codigoImagenPk')
            ->addSelect('m.identificador')
            ->where('m.codigoImagenPk <> 0');
        if ($session->get('filtroDocImagenIdentificador') != '') {
            $queryBuilder->andWhere("m.identificador = {$session->get('filtroDocImagenIdentificador')}");
        }
        return $queryBuilder;
    }

    public function listaArchivo($tipo, $codigo)
    {
        $session = new Session();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Imagen::class, 'm')
            ->select('m.codigoImagenPk')
            ->addSelect('m.identificador')
            ->where("m.codigoImagenTipoFk = '" . $tipo . "'")
            ->andWhere("m.identificador = '" . $codigo . "'");
        return $queryBuilder;
    }
}