<?php


namespace App\Tests\Units;


use App\Entity\Recipe;
use App\Entity\User;
use App\Tests\AbstractUnitTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeUnitTest extends KernelTestCase
{
    use AbstractUnitTest;

    public function testRecipeTitle()
    {
        $recipe = new Recipe();
        $recipe->setTitle('Un titre');

        $this->assertIsString($recipe->getTitle());
        $this->assertGreaterThan(3, strlen($recipe->getTitle()));
        $this->assertHasError($this->getRecipeEntity()->setTitle($recipe->getTitle()), 0);
    }

    public function testInvalidRecipeTitle()
    {
        $recipe = new Recipe();
        $recipe->setTitle(12);

        $this->assertIsString($recipe->getTitle());
        $this->assertLessThan(3, strlen($recipe->getTitle()));
        $this->assertHasError($this->getRecipeEntity()->setTitle($recipe->getTitle()), 1);
    }

    public function testRecipeDescription()
    {
        $recipe = new Recipe();
        $recipe->setDescription('Une description');

        $this->assertIsString($recipe->getDescription());
        $this->assertGreaterThan(6, strlen($recipe->getDescription()));
        $this->assertHasError($this->getRecipeEntity()->setDescription($recipe->getDescription()), 0);
    }

    public function testInvalidRecipeDescription()
    {
        $recipe = new Recipe();
        $recipe->setDescription(12);

        $this->assertIsString($recipe->getDescription());
        $this->assertLessThan(3, strlen($recipe->getDescription()));
        $this->assertHasError($this->getRecipeEntity()->setDescription($recipe->getDescription()), 1);
    }

    public function testRecipeImgUrl()
    {
        $recipe = new Recipe();
        $recipe->setImgUrl('http://placehold.it/300x300/');

        $this->assertIsString($recipe->getImgUrl());
        $this->assertRegExp('/(http|https):\/\/[a-z0-9]+[a-z0-9_\/]*/', $recipe->getImgUrl(), 'https or https only');
        $this->assertHasError($this->getRecipeEntity()->setDescription($recipe->getImgUrl()), 0);
    }

    public function testInvalidRecipeImgUrl()
    {
        $recipe = new Recipe();
        $recipe->setImgUrl(12);

        $this->assertIsString($recipe->getImgUrl());
        $this->assertNotRegExp('/(http|https):\/\/[a-z0-9]+[a-z0-9_\/]*/', $recipe->getImgUrl(), 'https or https only');
    }

    public function testRecipeAuthor()
    {
        $recipe = new Recipe();
        $recipe->setAuthor($this->getUserEntity());

        $this->assertInstanceOf(User::class, $recipe->getAuthor());
        $this->assertHasError($this->getRecipeEntity()->setAuthor($this->getUserEntity()), 0);
    }

    public function testInvalidRecipeAuthor()
    {
        $recipe = new Recipe();

        $recipe->setAuthor($this->getUserEntity());
        $this->assertNotInstanceOf(Recipe::class, $recipe->getAuthor());

    }

    public function getRecipeEntity(): Recipe
    {
        return (new Recipe())
            ->setTitle('Un titre')
            ->setDescription('Une descriptions')
            ->setImgUrl('http://placehold.it/300x300')
            ->setAuthor($this->getUserEntity())
            ;
    }

}
