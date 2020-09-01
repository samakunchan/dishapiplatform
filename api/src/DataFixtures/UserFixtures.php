<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    public const USER = 'Moi même';

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('sam@test.fr');
        $user->setPassword($this->encoder->encodePassword($user, '123'));
        $user->setRoles(['ROLE_ADMIN']);
        $profile = new Profile();
        $profile->setAddressOrg('537 rue du pré aux clercs');
        $profile->setCode('34090');
        $profile->setOrganisation('Samakunchan Technology');
        $profile->setUrlOrg('https://samakunchan-technology.com');
        $profile->setLogo('');
        $user->setProfile($profile);
        $user->setCreatedAt(new DateTimeImmutable());
        $manager->persist($user);
        $this->addReference(self::USER, $user);
        $manager->flush();
    }
}
