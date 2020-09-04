<?php


namespace App\Tests\Units;


use App\Entity\Ingredient;
use App\Tests\AbstractUnitTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IngredientUnitTest extends KernelTestCase
{
    use AbstractUnitTest;

    public function testOrganisation()
    {
        $ingredient = new Ingredient();
        $ingredient->setName('Un titre');

        $this->assertIsString($ingredient->getName());
        $this->assertGreaterThan(3, strlen($ingredient->getName()));
        $this->assertHasError($this->getIngredientEntity()->setName($ingredient->getName()), 0);
    }

    public function testInvalidOrganisation()
    {
        $ingredient = new Ingredient();
        $ingredient->setName(12);

        $this->assertIsString($ingredient->getName());
        $this->assertLessThan(3, strlen($ingredient->getName()));
        $this->assertHasError($this->getIngredientEntity()->setName($ingredient->getName()), 1);
    }
    public function getIngredientEntity(): Ingredient
    {
        return (new Ingredient());
    }
}
