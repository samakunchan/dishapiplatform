<?php


namespace App\Tests\Functionals;


use App\Entity\Profile;
use App\Tests\AbstractFuncTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileFuncTest extends WebTestCase
{
    use AbstractFuncTest;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->setServerParameter('HTTP_ACCEPT', sprintf('application/ld+json'));
        $this->client->setServerParameter('HTTP_CONTENT_TYPE', sprintf('application/ld+json; charset=UTF-8'));
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
    }

    public function testGetProfilesCollection(): void
    {
        $this->getEndPoint('/api/profiles', Request::METHOD_GET, Response::HTTP_OK);
    }

    public function testUpdateProfile(): void
    {
        $json = json_encode(['addressOrg' => '11 rue de pierpoljak', 'code' => '34090', 'urlOrg' => 'http://super-company.fr', 'logo' => 'http://placehold.it/300x300']);
        $profile = $this->client->getContainer()->get('doctrine')->getRepository(Profile::class)->findOneBy(['organisation' => 'Samakunchan Technology']);
        $this->endPointWithJsonData('/api/profiles/'.$profile->getid(), Request::METHOD_PUT, Response::HTTP_OK, $json);
    }
}
