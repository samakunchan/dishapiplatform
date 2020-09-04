<?php


namespace App\Tests\Functionals;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Tests\AbstractFuncTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IngredientFuncTest extends WebTestCase
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

    public function testGetIngredientsCollection(): void
    {
        $this->getEndPoint('/api/ingredients', Request::METHOD_GET, Response::HTTP_OK);
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $allingredients = $this->client->getContainer()->get('doctrine')->getRepository(Ingredient::class)->findAll();
        $this->assertCount(count($allingredients), $content['hydra:member']);
        foreach ($content['hydra:member'] as $data) {
            $this->andTestEachIteration($data);
        }
    }

    public function testCreateIngredient()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $recipe = $this->client->getContainer()->get('doctrine')->getRepository(Recipe::class)->findOneBy(['title'=> 'Ti jak boucané']);
        $json = json_encode([
            'name' => 'Jambon',
            'recipe' => '/api/recipes/'.$recipe->getUid()
        ]);
        $this->endPointWithJsonData('/api/ingredients', Request::METHOD_POST, Response::HTTP_CREATED, $json);
    }

    public function testCreateInvalidIngredient(): void
    {
        $json = json_encode([
            'name' => 'Ja'
        ]);
        $this->endPointWithJsonData('/api/ingredients', Request::METHOD_POST, Response::HTTP_BAD_REQUEST, $json);
    }

    public function testUpdateIngredient()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $ingredient = $this->client->getContainer()->get('doctrine')->getRepository(Ingredient::class)->findOneBy(['name' => 'Jambon']);
        $json = json_encode(['name' => 'chocolat']);

        $this->endPointWithJsonData('/api/ingredients/'.$ingredient->getId(), Request::METHOD_PUT, Response::HTTP_OK, $json);
    }

    public function testDeleteIngredient()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $ingredient = $this->client->getContainer()->get('doctrine')->getRepository(Ingredient::class)->findOneBy(['name' => 'chocolat']);
        $this->deleteEndPoint('/api/ingredients/'.$ingredient->getId(), Request::METHOD_DELETE, Response::HTTP_NO_CONTENT);
    }

    public function testWrongEndPointIngredient()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $this->getEndPoint('/api/ingredients/9999', Request::METHOD_GET, Response::HTTP_NOT_FOUND);
    }

    public function andTestEachIteration(array $datas)
    {
        $this->getEndPoint('/api/ingredients/'.$datas['id'], Request::METHOD_GET, Response::HTTP_OK);
    }
}
