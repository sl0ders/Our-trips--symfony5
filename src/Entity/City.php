<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CityRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 * @Vich\Uploadable
 */
#[ApiResource()]
class City
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Length(min: 2, minMessage: 'constraints.city.name.minLength'),
        Assert\NotBlank(message: "constraints.city.name.notblank")]
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[Length(min: 5, minMessage: 'constraints.description.name.minLength')]
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="cities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="city", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $pictures;

    /**
     * @Vich\UploadableField(mapping="maps", fileNameProperty="map.name", size="map.size", mimeType="map.mimeType", originalName="map.originalName", dimensions="map.dimensions")
     * @var File|null
     */
    private $mapFile;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $map;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTimeInterface|null
     */
    private $updatedAt;


    /**
     * @Vich\UploadableField(mapping="icons", fileNameProperty="icon.name", size="icon.size", mimeType="icon.mimeType", originalName="icon.originalName", dimensions="icon.dimensions")
     * @var File|null
     */
    private $iconFile;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $icon;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->map = new EmbeddedFile();
        $this->icon = new EmbeddedFile();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setCity($this);
        }
        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getCity() === $this) {
                $picture->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @param File|UploadedFile|null $mapFile
     */
    public function setMapFile(?File $mapFile = null)
    {
        $this->mapFile = $mapFile;

        if (null !== $mapFile) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getMapFile(): ?File
    {
        return $this->mapFile;
    }

    public function setMap(EmbeddedFile $map): void
    {
        $this->map = $map;
    }

    public function getMap(): ?EmbeddedFile
    {
        return $this->map;
    }

    /**
     * @param File|UploadedFile|null $iconFile
     */
    public function setIconFile(?File $iconFile = null)
    {
        $this->iconFile = $iconFile;

        if (null !== $iconFile) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getIconFile(): ?File
    {
        return $this->iconFile;
    }

    public function setIcon(EmbeddedFile $icon): void
    {
        $this->map = $icon;
    }

    public function getIcon(): ?EmbeddedFile
    {
        return $this->icon;
    }

    public function __toString()
    {
        return $this->name;
    }
}
