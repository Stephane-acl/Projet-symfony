<?php


namespace App\DataFixtures;


use App\Entity\Actor;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln' => ['program_0', 'program_5'],
        'Norman Reedus' => ['program_0'],
        'Lauren Cohan' => ['program_0'],
        'Danai Gurira' => ['program_0'],
    ];

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    function load(ObjectManager $manager)
    {
        $i = 0;
        $slugify = new Slugify();

        foreach (self::ACTORS as $name => $data) {
            $actor = new Actor();
            $actor->setName($name);
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            foreach ($data as $program) {
                $actor->addProgram($this->getReference($program));
            }
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
            $i++;
        }

        $faker  =  Faker\Factory::create('fr_FR');
        for ($i = 4; $i < 55; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->setPicture($faker->imageUrl(350, 350));
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
            $actor->addProgram($this->getReference('program_'.random_int(0,4)));
        }
        $manager->flush();
    }
}