<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\StepRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=StepRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"step:list"}}
 *          },
 *          "post"
 *      },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"step:details"}}
 *          },
 *          "put",
 *          "patch",
 *          "delete"
 *     }
 * )
 */
class Step
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"step:list", "step:details"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"step:list", "step:details"})
     */
    private string $uid;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min="6", minMessage="La description des étapes doit avoir au moins {{ limit }} caractères.")
     * @Groups({"step:list", "step:details", "recipe:details"})
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="steps")
     * @Groups({"step:details"})
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
