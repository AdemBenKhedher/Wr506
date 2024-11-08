<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use Symfony\Component\Validator\Constraints as Assert;




#[ApiResource]
#[ApiFilter(OrderFilter::class, properties:['id', 'lastname', 'firstname', 'dob', 'awards', 'bio', 'national', 'media'])]
#[ApiFilter(ExistsFilter::class, properties: ['dod'])]
#[ApiFilter(SearchFilter::class, properties: ['lastname' => 'partial', 'firstname' => 'partial', 'movies.title' => 'partial'])]
#[APiFilter(RangeFilter::class, properties: ['awards'])]

#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[ApiResource]
#[ORM\HasLifecycleCallbacks]
class Actor
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(
        min: 2,max: 255,
        minMessage: 'Your username must be at least {{ limit }} characters long',
        maxMessage: 'Your usernanme cannot be longer than {{ limit }} characters',
        )]
        #[Assert\NotNull(
         message: "The value {{ value }} must be not null."
        )]
        #[Assert\Type(
        "string",
        message: "The value {{ value }} is not a valid {{ type }}."
        )]
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
    min: 2,max: 255,
    minMessage: 'Your username must be at least {{ limit }} characters long',
    maxMessage: 'Your usernanme cannot be longer than {{ limit }} characters',
    )]
    #[Assert\NotNull]
    #[Assert\Type(
    "string",
    message: "The value {{ value }} is not a valid {{ type }}."
    )]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(
    message: "The value {{ value }} must be not null."
    )]
    #[Assert\Type(
    "\DateTimeInterface",
    message: "The value {{ value }} is not a valid {{ type }}."
    )]
    private ?\DateTimeInterface $dob = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type("int",
    message: "The value {{ value }} is not a valid {{ type }}."
    )]
    #[Assert\NotBlank]
    #[Assert\Range(
    min: 0,
    notInRangeMessage: 'The number of awards must be not null',
    )]
    private ?int $awards = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255)]
    #[Assert\Country(
    message: "The value {{ value }} is not a valid country."
    )]
    #[Assert\NotNull(
    message: "The value {{ value }} must be not null."
    )]
    private ?string $nationalty = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $media = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Choice(choices:['male','female','other'],
    message: "The value {{ value }} is not one of  {{ choices }}.")]
    private ?string $gender = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'actors', cascade: ['persist'])]
    private Collection $movies;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface",
    message: "The value {{ value }} is not a valid {{ type }}."
    )]
    private ?\DateTimeInterface $dod = null;





    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): static
    {
        $this->dob = $dob;

        return $this;
    }

    public function getAwards(): ?int
    {
        return $this->awards;
    }

    public function setAwards(?int $awards): static
    {
        $this->awards = $awards;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getNationalty(): ?string
    {
        return $this->nationalty;
    }

    public function setNationalty(string $nationalty): static
    {
        $this->nationalty = $nationalty;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): static
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): static
    {
        $this->movies->removeElement($movie);

        return $this;
    }

    public function getDod(): ?\DateTimeInterface
    {
        return $this->dod;
    }

    public function setDod(?\DateTimeInterface $dod): static
    {
        $this->dod = $dod;

        return $this;
    }




}
