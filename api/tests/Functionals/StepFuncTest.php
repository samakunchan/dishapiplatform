<?php


namespace App\Tests\Functionals;


use App\Entity\Recipe;
use App\Entity\Step;
use App\Tests\AbstractFuncTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StepFuncTest extends WebTestCase
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

    public function testGetStepsCollection(): void
    {
        $this->getEndPoint('/api/steps', Request::METHOD_GET, Response::HTTP_OK);
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $allSteps = $this->client->getContainer()->get('doctrine')->getRepository(Step::class)->findAll();
        $this->assertCount(count($allSteps), $content['hydra:member']);

        foreach ($content['hydra:member'] as $data) {
            $this->andTestEachIteration($data);
        }
    }

    public function testCreateStep()
    {
        $recipe = $this->client->getContainer()->get('doctrine')->getRepository(Recipe::class)->findOneBy(['title'=> 'Ti jak boucané']);
        $json = json_encode([
            'description' => 'string',
            'recipe' => '/api/recipes/'.$recipe->getUid()
        ]);
        $this->endPointWithJsonData('/api/steps', Request::METHOD_POST, Response::HTTP_CREATED, $json);
    }

    public function testCreateInvalidStep(): void
    {
        $json = json_encode([
            'description' => '123'
        ]);
        $this->endPointWithJsonData('/api/steps', Request::METHOD_POST, Response::HTTP_BAD_REQUEST, $json);
    }

    public function testUpdateStep()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $ingredient = $this->client->getContainer()->get('doctrine')->getRepository(Step::class)->findOneBy(['description' => 'string']);
        $json = json_encode(['description' => 'chocolat']);

        $this->endPointWithJsonData('/api/steps/'.$ingredient->getId(), Request::METHOD_PUT, Response::HTTP_OK, $json);
    }

    public function testDeleteStep()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $ingredient = $this->client->getContainer()->get('doctrine')->getRepository(Step::class)->findOneBy(['description' => 'chocolat']);
        $this->deleteEndPoint('/api/steps/'.$ingredient->getId(), Request::METHOD_DELETE, Response::HTTP_NO_CONTENT);
    }

    public function testWrongEndPointStep()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $this->getEndPoint('/api/steps/9999', Request::METHOD_GET, Response::HTTP_NOT_FOUND);
    }

    public function andTestEachIteration(array $datas)
    {
        $this->getEndPoint('/api/steps/'.$datas['id'], Request::METHOD_GET, Response::HTTP_OK);
    }
}
