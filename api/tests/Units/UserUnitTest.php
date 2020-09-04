<?php


namespace App\Tests\Units;


use App\Entity\Profile;
use App\Entity\Recipe;
use App\Entity\User;
use App\Tests\AbstractUnitTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserUnitTest extends KernelTestCase
{
    use AbstractUnitTest;

    public function setUp()
    {

    }
    public function testEmailIsValid()
    {
        $user = new User();
        $user->setEmail('sam@test.fr');

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsString($user->getEmail());
        $this->assertStringContainsString('@', $user->getEmail());
        $this->assertGreaterThan(6, strlen($user->getEmail()));
        $this->assertHasError($this->getUserEntity()->setEmail($user->getEmail()), 0);
    }

    public function testEmailIsNotValid()
    {
        $user = new User();
        $user->setEmail('sa@');
        $this->assertStringNotContainsString('#', $user->getEmail());
        $this->assertHasError($this->getUserEntity()->setEmail($user->getEmail()), 1);
    }

    public function testPasswordIsValid()
    {
        $user = new User();
        $user->setPassword('123456');

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsString($user->getPassword());
        $this->assertGreaterThanOrEqual(6, strlen($user->getPassword()));
        $this->assertHasError($this->getUserEntity()->setPassword($user->getPassword()), 0);
    }

    public function testPasswordIsNotValid()
    {
        $user = new User();
        $user->setPassword(12);
        $this->assertHasError($this->getUserEntity()->setPassword($user->getPassword()), 1);
    }

    public function testRoleIsValid()
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsArray($user->getRoles());
        $this->assertSameSize(['ROLE_USER'], $user->getRoles());
        $this->assertIsArray($user->getRoles());
        $this->assertHasError($this->getUserEntity()->setRoles($user->getRoles()), 0);
    }

    public function testRoleIsNotValid()
    {
        $user = new User();
        $user->setRoles(['ROLE_']);

        $this->assertNotSameSize(['ROLE_USER'], $user->getRoles());

        $this->assertHasError($this->getUserEntity()->setRoles($user->getRoles()), 0);
    }

    public function testUserProfile()
    {
        $user = new User();
        $user->setProfile(new Profile());

        $this->assertInstanceOf(Profile::class, $user->getProfile());
    }

    public function testInvalidUserProfile()
    {
        $user = new User();
        $user->setProfile(null);

        $this->assertNotInstanceOf(Profile::class, $user->getProfile());
    }

    public function testUserRecipe()
    {
        $user = new User();
        $user->addRecipe(new Recipe());

        $this->assertInstanceOf(Recipe::class, $user->getRecipes()[0]);
    }


}
