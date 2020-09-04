<?php


namespace App\Tests\Units;


use App\Entity\Step;
use App\Tests\AbstractUnitTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StepUnitTest extends KernelTestCase
{
    use AbstractUnitTest;

    public function testOrganisation()
    {
        $step = new Step();
        $step->setDescription('Un titre');

        $this->assertIsString($step->getDescription());
        $this->assertGreaterThan(6, strlen($step->getDescription()));
        $this->assertHasError($this->getStepEntity()->setDescription($step->getDescription()), 0);
    }

    public function testInvalidOrganisation()
    {
        $step = new Step();
        $step->setDescription(12);

        $this->assertIsString($step->getDescription());
        $this->assertLessThan(6, strlen($step->getDescription()));
        $this->assertHasError($this->getStepEntity()->setDescription($step->getDescription()), 1);
    }
    public function getStepEntity(): Step
    {
        return (new Step());
    }
}
