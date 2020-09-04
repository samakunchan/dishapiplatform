<?php


namespace App\Tests\Functionals;


use App\Entity\User;
use App\Tests\AbstractFuncTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserFuncTest extends WebTestCase
{
    use AbstractFuncTest;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->setServerParameter('HTTP_ACCEPT', sprintf('application/ld+json'));
        $this->client->setServerParameter('HTTP_CONTENT_TYPE', sprintf('application/ld+json; charset=UTF-8'));
    }

    public function testGetUsersCollection(): void
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');

        $this->getEndPoint('/api/users', Request::METHOD_GET, Response::HTTP_OK);
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $allUsers = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findAll();
        $this->assertCount(count($allUsers), $content['hydra:member']);

        foreach ($content['hydra:member'] as $data) {
            $this->whatWeShowToUser($data, ['uid']);
            $this->whatWeDontShowToUser($data, ['id', 'password', 'roles']);
            $this->andTestEachIteration($data);
        }
    }

    public function testCreateUser(): void
    {
        $json = json_encode(['email' => 'user-created@test-environnement.fr', 'password' => '123']);
        $this->endPointWithJsonData('/api/users', Request::METHOD_POST, Response::HTTP_CREATED, $json);
    }

    public function testCreateInvalidUser(): void
    {
        $json = json_encode(['email' => 'user-created', 'password' => '123']);
        $this->endPointWithJsonData('/api/users', Request::METHOD_POST, Response::HTTP_BAD_REQUEST, $json);
    }

    public function testUpdateCurrentUser()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'admin@test-environnement.fr']);
        $json = json_encode(['password' => '123']);

        $this->endPointWithJsonData('/api/users/'.$user->getUid(), Request::METHOD_PUT, Response::HTTP_OK, $json);
    }

    public function testUpdateDifferentUser()
    {
        $this->forRandomUser();
        $this->forAdmin();
    }

    public function testDeleteUser()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'user-created@test-environnement.fr']);
        $this->deleteEndPoint('/api/users/'.$user->getUid(), Request::METHOD_DELETE, Response::HTTP_NO_CONTENT);
        $this->getEndPoint('/api/profiles/'.$user->getProfile()->getId(), Request::METHOD_GET, Response::HTTP_NOT_FOUND);
    }

    public function testWrongEndPointUser()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $this->getEndPoint('/api/users/9999', Request::METHOD_GET, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function andTestEachIteration(array $datas)
    {
        $this->getEndPoint('/api/users/'.$datas['uid'], Request::METHOD_GET, Response::HTTP_OK);
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->whatWeShowToUser($content, ['roles', 'profile', 'recipes']);
        $this->whatWeDontShowToUser($content, ['id', 'password']);
    }
}
