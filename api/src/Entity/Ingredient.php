<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IngredientRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"ingredient:list"}}
 *          },
 *          "post"
 *      },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"ingredient:details"}}
 *          },
 *          "put",
 *          "patch",
 *          "delete"
 *     }
 * )
 */
class Ingredient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"ingredient:list", "ingredient:details"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"ingredient:list", "ingredient:details"})
     */
    private string $uid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="3", minMessage="Le nom de l'ingrédient doit avoir au moins {{ limit }} caractères.")
     * @Groups({"ingredient:list", "ingredient:details", "recipe:details"})
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="ingredients")
     * @Groups({"ingredient:details"})
     */
    private ?Recipe $recipe;

    public function __construct()
    {
        $this->uid = Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getUid()
    {
        return $this->uid;
    }
}
