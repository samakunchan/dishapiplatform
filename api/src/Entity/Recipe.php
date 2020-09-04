<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 * @ApiResource(
 *      collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"recipe:list"}}
 *          },
 *          "post"={
 *              "normalization_context"={"groups"={"recipe:post"}}
 *          }
 *      },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"recipe:details"}}
 *          },
 *          "put",
 *          "patch",
 *          "delete"
 *     },
 *     denormalizationContext={"groups"={"recipe:post"}}
 * )
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={
 *          "title": "ipartial"
 *      }
 * )
 */
class Recipe
{
    use Timestapable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private ?int $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"recipe:list", "recipe:details"})
     * @ApiProperty(identifier=true)
     */
    private UuidInterface $uid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="3", minMessage="Le nom de l'ingrédient doit avoir au moins {{ limit }} caractères.")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Type("string")
     * @Groups({"user:details", "recipe:list", "recipe:details", "recipe:post"})
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"recipe:list", "recipe:details", "recipe:post"})
     */
    private string $imgUrl;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type("string")
     * @Groups({"recipe:list", "recipe:details"})
     */
    private string $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     * @Assert\Length(min="6", minMessage="La description doit avoir au moins {{ limit }} caractères.")
     * @Assert\Type("string")
     * @Groups({"recipe:details", "recipe:post"})
     */
    private string $description;

    /**
     * @ORM\OneToMany(targetEntity=Ingredient::class, mappedBy="recipe", orphanRemoval=true, cascade={"persist", "remove"})
     * @Assert\Type("object")
     * @Groups({"recipe:details"})
     */
    private Collection $ingredients;

    /**
     * @ORM\OneToMany(targetEntity=Step::class, mappedBy="recipe", orphanRemoval=true, cascade={"persist", "remove"})
     * @Assert\Type("object")
     * @Groups({"recipe:details"})
     */
    private Collection $steps;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recipes")
     * @Groups({"recipe:details", "recipe:post"})
     */
    private UserInterface $author;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->steps = new ArrayCollection();
        $this->uid = Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $cleanText = preg_replace('/\W+/', '-', $slug);
        $cleanText = strtolower(trim($cleanText, '-'));
        $this->slug = $cleanText;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Ingredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->contains($ingredient)) {
            $this->ingredients->removeElement($ingredient);
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipe() === $this) {
                $ingredient->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Step[]
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->contains($step)) {
            $this->steps->removeElement($step);
            // set the owning side to null (unless already changed)
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
        }

        return $this;
    }

    public function getAuthor(): UserInterface
    {
        return $this->author;
    }

    public function setAuthor(UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getUid()
    {
        return $this->uid;
    }
}
