<?php


namespace App\Tests\Units;


use App\Entity\Profile;
use App\Tests\AbstractUnitTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileUnitTest extends KernelTestCase
{
    use AbstractUnitTest;

    public function testOrganisation()
    {
        $profile = new Profile();
        $profile->setOrganisation('Un titre');

        $this->assertIsString($profile->getOrganisation());
        $this->assertGreaterThan(3, strlen($profile->getOrganisation()));
        $this->assertHasError($this->getProfileEntity()->setOrganisation($profile->getOrganisation()), 0);
    }

    public function testInvalidOrganisation()
    {
        $profile = new Profile();
        $profile->setOrganisation(12);

        $this->assertIsString($profile->getOrganisation());
        $this->assertLessThan(3, strlen($profile->getOrganisation()));
        $this->assertHasError($this->getProfileEntity()->setOrganisation($profile->getOrganisation()), 1);
    }

    public function testAdress()
    {
        $profile = new Profile();
        $profile->setAddressOrg('Un titre');

        $this->assertIsString($profile->getAddressOrg());
        $this->assertGreaterThan(3, strlen($profile->getAddressOrg()));
        $this->assertHasError($this->getProfileEntity()->setAddressOrg($profile->getAddressOrg()), 0);
    }

    public function testInvalidAdress()
    {
        $profile = new Profile();
        $profile->setAddressOrg(12);

        $this->assertIsString($profile->getAddressOrg());
        $this->assertLessThan(3, strlen($profile->getAddressOrg()));
        $this->assertHasError($this->getProfileEntity()->setAddressOrg($profile->getAddressOrg()), 1);
    }

    public function testPostalCode()
    {
        $profile = new Profile();
        $profile->setCode('97421');

        $this->assertIsString($profile->getCode());
        $this->assertGreaterThan(3, strlen($profile->getCode()));
        $this->assertHasError($this->getProfileEntity()->setCode($profile->getCode()), 0);
    }

    public function testInvalidPostalCode()
    {
        $profile = new Profile();
        $profile->setCode(12);

        $this->assertIsString($profile->getCode());
        $this->assertLessThan(3, strlen($profile->getCode()));
        $this->assertHasError($this->getProfileEntity()->setCode($profile->getCode()), 1);
    }

    public function testUrlOrg()
    {
        $profile = new Profile();
        $profile->setUrlOrg('https://mon-url.com');

        $this->assertIsString($profile->getUrlOrg());
        $this->assertGreaterThan(3, strlen($profile->getUrlOrg()));
        $this->assertHasError($this->getProfileEntity()->setUrlOrg($profile->getUrlOrg()), 0);
    }

    public function testInvalidUrlOrg()
    {
        $profile = new Profile();
        $profile->setUrlOrg(12);

        $this->assertIsString($profile->getUrlOrg());
        $this->assertLessThan(3, strlen($profile->getUrlOrg()));
        $this->assertHasError($this->getProfileEntity()->setUrlOrg($profile->getUrlOrg()), 1);
    }

    public function testUrlLogo()
    {
        $profile = new Profile();
        $profile->setLogo('https://mon-logo.com');

        $this->assertIsString($profile->getLogo());
        $this->assertGreaterThan(3, strlen($profile->getLogo()));
        $this->assertHasError($this->getProfileEntity()->setLogo($profile->getLogo()), 0);
    }

    public function testInvalidUrlLogo()
    {
        $profile = new Profile();
        $profile->setLogo(12);

        $this->assertIsString($profile->getLogo());
        $this->assertLessThan(3, strlen($profile->getLogo()));
        $this->assertHasError($this->getProfileEntity()->setLogo($profile->getLogo()), 1);
    }

    public function getProfileEntity(): Profile
    {
        return (new Profile());
    }
}
