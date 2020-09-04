<?php


namespace App\Tests;

use App\Entity\User;
use Symfony\Component\Validator\ConstraintViolation;

trait AbstractUnitTest
{
    /**
     * @param $entity
     * @param $data
     */
    public function assertHasError($entity, $data)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($entity);
        $messages = [];
        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors as $error){
            $messages[] = $error->getPropertyPath(). ' => '.$error->getMessage();
        }
        $this->assertCount($data, $errors, implode(', ', $messages));
    }

    /**
     * @return User
     */
    public function getUserEntity(): User
    {
        return (new User())
            ->setEmail('sam@test.fr')
            ->setPassword('123456')
            ->setRoles(['ROLE_ADMIN'])
            ;
    }

}
