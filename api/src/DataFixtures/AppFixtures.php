<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;

use App\Entity\Recipe;
use App\Entity\Step;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        $faker->addProvider(new Restaurant($faker));
        for($i = 0; $i < 10; $i++ ){
            $recipe = new Recipe();
            $recipe->setTitle($faker->foodName());
            $recipe->setDescription($faker->sentence);
            $recipe->setImgUrl('http://placeimg.com/640/480/nature');
            $recipe->setAuthor($this->getReference(UserFixtures::USER));
            $recipe->setSlug($recipe->getTitle());
            $recipe->setCreatedAt($faker->dateTime($max = 'now', $timezone = 'UTC'));


            for ($j = 0; $j < $faker->numberBetween(3, 10); $j++ ) {
                $ingredient = new Ingredient();
                $ingredient->setName($faker->meatName());
                $recipe->addIngredient($ingredient);
                $manager->persist($ingredient);
            }
            for($k = 0; $k < $faker->numberBetween(2, 5); $k++ ){
                $step = new Step();
                $step->setDescription($faker->sentence);
                $recipe->addStep($step);
                $manager->persist($step);
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
