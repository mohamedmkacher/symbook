<?php

namespace App\DataFixtures;

use App\Entity\Livres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LivreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 100; $i < 500; $i++) {
            $livre = new Livres();
            $titre = substr($faker->sentence($faker->numberBetween(1,3)),0,-1);
            $livre->setTitre($titre)
                ->setSlug(str_replace(" ", "-", strtolower(($titre))))
                ->setIsbn($faker->isbn13())
                ->setPrix($faker->randomFloat(3, 10, 50))
                ->setEditeur($faker->company())
                ->setResume($faker->realText())
                ->setDateEdition($faker->dateTimeBetween('-5 year', 'now'))
                ->setImage("https://picsum.photos/300/?id=" . $i);


            $manager->persist($livre);

        }
        $manager->flush();
    }
}
