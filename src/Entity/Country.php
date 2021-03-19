<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 * @Vich\Uploadable
 */
class Country
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
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=City::class, mappedBy="country", orphanRemoval=true)
     */
    private $cities;

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
     * @ORM\Column(type="datetime")
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
        $this->cities = new ArrayCollection();
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

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setCountry($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getCountry() === $this) {
                $city->setCountry(null);
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
