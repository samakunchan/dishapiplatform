<?php


namespace App\Tests;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait AbstractFuncTest
{
    /**
     * Create a client with a default Authorization header.
     * ALERT Dans la documenation il y a les underscores à enlever a username et password
     * @param string $username
     * @param string $password
     * @return KernelBrowser
     */
    protected function createAuthenticatedClient($username = 'user', $password = 'password'): KernelBrowser
    {
        $this->client->request(
            Request::METHOD_POST, '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $this->client;
    }

    /**
     * @param string $url
     * @param string $method
     * @param int $expectedStatusResponse
     * @return KernelBrowser
     */
    protected function getEndPoint(string $url, string $method, int $expectedStatusResponse): KernelBrowser
    {
        $this->client->request($method, $url, [], [], []);
        $this->assertSame($expectedStatusResponse, $this->client->getResponse()->getStatusCode());
        $this->assertResponseStatusCodeSame($expectedStatusResponse);
        $this->assertEquals($expectedStatusResponse, $this->client->getResponse()->getStatusCode());

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        if ($expectedStatusResponse !== 404 && $expectedStatusResponse !== 500) {
            $this->assertResponseIsSuccessful(sprintf('The %s public URL loads correctly.', $url));
        }

        return $this->client;
    }

    protected function endPointWithJsonData(string $url, string $method, int $expectedStatusResponse, string $json): KernelBrowser
    {
        $this->client->request($method, $url, [], [], ['CONTENT_TYPE' => 'application/ld+json'], $json);
        $this->assertSame($expectedStatusResponse, $this->client->getResponse()->getStatusCode());
        $this->assertResponseStatusCodeSame($expectedStatusResponse);
        $this->assertEquals($expectedStatusResponse, $this->client->getResponse()->getStatusCode());

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        if ($expectedStatusResponse !== 400 && $expectedStatusResponse !== 401) {
            $this->assertResponseIsSuccessful(sprintf('The %s public URL has post correctly.', $url));
        }

        return $this->client;
    }

    protected function deleteEndPoint(string $url, string $method, int $expectedStatusResponse)
    {
        $this->client->request($method, $url, [], [], ['CONTENT_TYPE' => 'application/ld+json']);
        $this->assertSame($expectedStatusResponse, $this->client->getResponse()->getStatusCode());
        $this->assertResponseStatusCodeSame($expectedStatusResponse);
        $this->assertEquals($expectedStatusResponse, $this->client->getResponse()->getStatusCode());
    }

    public function whatWeShowToUser(array $datas, array $keysExpected): void
    {
        foreach ($keysExpected as $value) {
            $this->assertArrayHasKey($value, $datas);
        }
    }

    public function whatWeDontShowToUser(array $datas, array $keysNotExpected): void
    {
        foreach ($keysNotExpected as $value) {
            $this->assertArrayNotHasKey($value, $datas);
        }
    }


    public function forAdmin()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'user@test-environnement.fr']);
        $json = json_encode(['password' => '123']);

        $this->endPointWithJsonData('/api/users/'.$user->getUid(), Request::METHOD_PUT, Response::HTTP_OK, $json);
    }

    public function forRandomUser()
    {
        $this->createAuthenticatedClient('user@test-environnement.fr', '123');
        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'random@test-environnement.fr']);
        $json = json_encode(['password' => '123']);

        $this->endPointWithJsonData('/api/users/'.$user->getUid(), Request::METHOD_PUT, Response::HTTP_UNAUTHORIZED, $json);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('It\'s not your resource', $content['hydra:description']);
    }
}
