<?php


namespace App\Repository;


use App\Entity\Archivo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

class ArchivoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Archivo::class);
    }

    public function lista()
    {
        $session = new Session();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Archivo::class, 'm')
            ->select('m.codigoMasivoPk')
            ->addSelect('m.identificador')
            ->where('m.codigoMasivoPk <> 0');
        if ($session->get('filtroDocMasivoIdentificador') != '') {
            $queryBuilder->andWhere("m.identificador = {$session->get('filtroDocMasivoIdentificador')}");
        }
        return $queryBuilder;
    }

    public function listaArchivo($tipo, $codigo)
    {
        $session = new Session();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Archivo::class, 'a')
            ->select('a.codigoArchivoPk')
            ->addSelect('a.codigo')
            ->addSelect('a.nombre')
            ->addSelect('a.fecha')
            ->addSelect('a.descripcion')
            ->addSelect('a.comentarios')
            ->addSelect('a.usuario')
            ->addSelect('a.codigoArchivoTipoFk')
            ->addSelect('a.directorio')
            ->addSelect('(a.tamano / 1000000) as tamano')
            ->where("a.codigoArchivoTipoFk = '" . $tipo . "'")
            ->andWhere("a.codigo = '" . $codigo . "'");
        return $queryBuilder;
    }

    public function listaArchivoAlmacenamiento($codigoPadre)
    {
        $session = new Session();
        if ($codigoPadre) {
            $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Archivo::class, 'a')
                ->select('a.codigoArchivoPk')
                ->addSelect('a.codigo')
                ->addSelect('a.nombre')
                ->addSelect('a.fecha')
                ->addSelect('a.descripcion')
                ->addSelect('a.comentarios')
                ->addSelect('a.usuario')
                ->addSelect('a.codigoArchivoTipoFk')
                ->addSelect('a.directorio')
                ->addSelect('a.codigoDirectorioFk')
                ->addSelect('(a.tamano / 1000000) as tamano')
                ->where("a.codigoDirectorioFk = {$codigoPadre}");
            return $queryBuilder;
        } else {
            return [];
        }
    }

    public function apiDescargar($tipo, $codigo) {
        $em = $this->getEntityManager();
        $arrConfiguracion = $em->getRepository(Configuracion::class)->archivoMasivo();
        $queryBuilder = $em->createQueryBuilder()->from(Archivo::class, 'a')
            ->select('a.codigoArchivoPk')
            ->addSelect('a.codigoArchivoTipoFk')
            ->addSelect('a.directorio')
            ->addSelect('a.nombre')
            ->where("a.codigoArchivoTipoFk = '{$tipo}'")
            ->andWhere("a.codigo = {$codigo}")
            ->setMaxResults(1);
        $arArchivos = $queryBuilder->getQuery()->execute();
        foreach ($arArchivos as $arArchivo) {
            $ruta = $arrConfiguracion['rutaAlmacenamiento'] . "/archivo/" . $arArchivo['codigoArchivoTipoFk'] . "/" . $arArchivo['directorio'] . "/" . $arArchivo['codigoArchivoPk'] . "_" . $arArchivo['nombre'];
            if(file_exists($ruta)) {
                $b64Doc = chunk_split(base64_encode(file_get_contents($ruta)));
                $arArchivos[0]['base64'] = $b64Doc;
                $arArchivos[0]['error'] = 0;
            }
        }
        return $arArchivos;
    }

}