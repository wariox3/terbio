<?php

namespace App\DataFixtures;

use App\Entity\FormatoTipo;
use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FixFormatoTipo extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $arFormatoTipo = $manager->getRepository(FormatoTipo::class)->find('CL');
        if(!$arFormatoTipo) {
            $arFormatoTipo = new FormatoTipo();
            $arFormatoTipo->setCodigoFormatoTipoPk('CL');
            $arFormatoTipo->setNombre("CARTA LABORAL");
            $manager->persist($arFormatoTipo);
        }
        $arFormatoTipo = $manager->getRepository(FormatoTipo::class)->find('RL');
        if(!$arFormatoTipo) {
            $arFormatoTipo = new FormatoTipo();
            $arFormatoTipo->setCodigoFormatoTipoPk('RL');
            $arFormatoTipo->setNombre("RETIRO LABORAL");
            $manager->persist($arFormatoTipo);
        }
        $arFormatoTipo = $manager->getRepository(FormatoTipo::class)->find('ECF');
        if(!$arFormatoTipo) {
            $arFormatoTipo = new FormatoTipo();
            $arFormatoTipo->setCodigoFormatoTipoPk('ECF');
            $arFormatoTipo->setNombre("ESTADO DE CUENTA FORMAL");
            $manager->persist($arFormatoTipo);
        }
        $manager->flush();
    }
}
