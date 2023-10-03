<?php


namespace App\DataFixtures;


use App\Entity\Idioma;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FixIdioma  extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $arrCanales = array(
            ['codigo' => 'ingles', 'nombre' => 'Ingles'],
            ['codigo' => 'frances', 'nombre' => 'Frances'],
            ['codigo' => 'portugues', 'nombre' => 'Portugués'],
            ['codigo' => 'espeñol', 'nombre' => 'Espeñol'],
        );
        foreach ($arrCanales as $arrCanal) {
            $arCanal = $manager->getRepository('App:Idioma')->find($arrCanal['codigo']);
            if (!$arCanal) {
                $arCanal = new \App\Entity\Idioma();
                $arCanal->setCodigoIdiomaPk($arrCanal['codigo']);
                $arCanal->setNombre($arrCanal['nombre']);
                $manager->persist($arCanal);
            }
        }
        $manager->flush();
    }
}