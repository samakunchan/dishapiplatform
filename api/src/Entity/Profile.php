<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"profil:list"}}
 *          }
 *      },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"profil:details"}}
 *          },
 *          "put",
 *          "patch",
 *          "delete"
 *     }
 * )
 */
class Profile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"profil:details"})
     */
    private UuidInterface $uid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="3", minMessage="Le nom de l'ingrédient doit avoir au moins {{ limit }} caractères.")
     * @Assert\Type("string")
     * @Groups({"profil:list", "profil:details"})
     */
    private string $organisation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="3", minMessage="Le nom de l'ingrédient doit avoir au moins {{ limit }} caractères.")
     * @Assert\Type("string")
     * @Groups({"profil:details"})
     */
    private string $addressOrg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="3", minMessage="Le code postale doit avoir au moin {{ limit }} caractères.")
     * @Assert\Type(type="string", message="La valeur {{ value }} n'est pas une valeur de type: {{ type }}.")
     * @Groups({"profil:details"})
     */
    private string $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="3", minMessage="Le nom de l'ingrédient doit avoir au moins {{ limit }} caractères.")
     * @Assert\Type("string")
     * @Groups({"profil:list", "profil:details"})
     */
    private string $urlOrg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"profil:list", "profil:details"})
     */
    private string $logo;

    public function __construct()
    {
        $this->uid = Uuid::uuid4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function setOrganisation(string $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getAddressOrg(): ?string
    {
        return $this->addressOrg;
    }

    public function setAddressOrg(string $addressOrg): self
    {
        $this->addressOrg = $addressOrg;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUrlOrg(): ?string
    {
        return $this->urlOrg;
    }

    public function setUrlOrg(string $urlOrg): self
    {
        $this->urlOrg = $urlOrg;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getUid()
    {
        return $this->uid;
    }
}
