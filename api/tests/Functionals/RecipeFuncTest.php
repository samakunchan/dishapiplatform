<?php


namespace App\Tests\Functionals;


use App\Entity\Recipe;
use App\Entity\User;
use App\Tests\AbstractFuncTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RecipeFuncTest extends WebTestCase
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

    public function testGetRecipesCollection(): void
    {
        $this->getEndPoint('/api/recipes', Request::METHOD_GET, Response::HTTP_OK);
        $content = json_decode($this->client->getResponse()->getContent(), true);

        $allRecipes = $this->client->getContainer()->get('doctrine')->getRepository(Recipe::class)->findAll();
        $this->assertCount(count($allRecipes), $content['hydra:member']);

        foreach ($content['hydra:member'] as $data) {
            $this->whatWeShowToUser($data, ['@id', '@type', 'uid', 'title', 'imgUrl', 'slug', 'createdAt']);
            $this->whatWeDontShowToUser($data, ['id', 'description', 'ingredients', 'steps', 'author']);
            $this->andTestEachIteration($data);
        }
    }

    public function testCreateRecipe()
    {
        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email'=> 'random@test-environnement.fr']);
        $json = json_encode([
            'title' => 'Un titre',
            'imgUrl' => 'http://placehold.it/300x300',
            'description' => 'Une description',
            'author' => '/api/users/'.$user->getUid()
        ]);
        $this->endPointWithJsonData('/api/recipes', Request::METHOD_POST, Response::HTTP_CREATED, $json);
    }

    public function testCreateInvalidRecipe(): void
    {
        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email'=> 'random@test-environnement.fr']);
        $json = json_encode([
            'title' => 'Un',
            'imgUrl' => 'http://placehold.it/300x300',
            'description' => 'Une description',
            'author' => '/api/users/'.$user->getUid()
        ]);
        $this->endPointWithJsonData('/api/recipes', Request::METHOD_POST, Response::HTTP_BAD_REQUEST, $json);
    }

    public function testUpdateRecipeForCurrentUser()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $recipe = $this->client->getContainer()->get('doctrine')->getRepository(Recipe::class)->findOneBy(['title' => 'Un titre']);
        $json = json_encode(['title' => 'Un titre modifier']);

        $this->endPointWithJsonData('/api/recipes/'.$recipe->getUid(), Request::METHOD_PUT, Response::HTTP_OK, $json);
    }

    public function testDeleteRecipe()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $recipe = $this->client->getContainer()->get('doctrine')->getRepository(Recipe::class)->findOneBy(['slug' => 'un-titre-modifier']);
        $this->deleteEndPoint('/api/recipes/'.$recipe->getUid(), Request::METHOD_DELETE, Response::HTTP_NO_CONTENT);
    }

    public function testWrongEndPointRecipe()
    {
        $this->createAuthenticatedClient('admin@test-environnement.fr', '123');
        $this->getEndPoint('/api/recipes/9999', Request::METHOD_GET, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function andTestEachIteration(array $datas)
    {
        $this->getEndPoint('/api/recipes/'.$datas['uid'], Request::METHOD_GET, Response::HTTP_OK);
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->whatWeShowToUser($content, ['@id', '@type', 'uid', 'title', 'imgUrl', 'slug', 'createdAt', 'description', 'ingredients', 'steps', 'author']);
    }
}
