<?php


namespace App\Repository;


use App\Entity\Directorio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DirectorioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Directorio::class);
    }

    public function devolverDirectorio($tipo, $clase)
    {
        $em = $this->getEntityManager();
        $directorio = "";
        $arDirectorio = $em->getRepository(Directorio::class)->findOneBy(array('tipo' => $tipo, 'clase' => $clase));
        if ($arDirectorio) {
            if ($arDirectorio->getNumeroArchivos() >= 50000) {
                $arDirectorio->setNumeroArchivos(1);
                $arDirectorio->setDirectorio($arDirectorio->getDirectorio() + 1);
                $em->persist($arDirectorio);
                $directorio = $arDirectorio->getDirectorio();
            } else {
                $arDirectorio->setNumeroArchivos($arDirectorio->getNumeroArchivos() + 1);
                $directorio = $arDirectorio->getDirectorio();
            }
        } else {
            $arDirectorio = new Directorio();
            $arDirectorio->setDirectorio(1);
            $arDirectorio->setNumeroArchivos(1);
            $arDirectorio->setTipo($tipo);
            $arDirectorio->setClase($clase);
            $em->persist($arDirectorio);
            $directorio = "1";
        }
        $em->flush();
        return $directorio;
    }

    public function lista($codigoPadre)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Directorio::class, 'd')
            ->select('d.codigoDirectorioPk')
            ->addselect('d.tipo')
            ->addselect('d.nombre')
            ->addSelect('d.clase')
            ->addSelect('d.directorio')
            ->addSelect('d.numeroArchivos')
            ->where("d.tipo = 'G'");
        if ($codigoPadre) {
            $queryBuilder->andWhere("d.codigoDirectorioPadreFk = {$codigoPadre}");
        } else {
            $queryBuilder->andWhere("d.codigoDirectorioPadreFk IS NULL");
        }
        $queryBuilder->addOrderBy('d.codigoDirectorioPk', 'DESC');
        return $queryBuilder->getQuery()->getResult();
    }
}