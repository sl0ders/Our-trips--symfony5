<?php

namespace App\Entity;

use App\Repository\MapRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MapRepository::class)
 */
class Map
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
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=Country::class, mappedBy="map", cascade={"persist", "remove"})
     */
    private $country;

    /**
     * @ORM\OneToOne(targetEntity=City::class, mappedBy="map", cascade={"persist", "remove"})
     */
    private $city;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        // set the owning side of the relation if necessary
        if ($country->getMap() !== $this) {
            $country->setMap($this);
        }

        $this->country = $country;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        // unset the owning side of the relation if necessary
        if ($city === null && $this->city !== null) {
            $this->city->setMap(null);
        }

        // set the owning side of the relation if necessary
        if ($city !== null && $city->getMap() !== $this) {
            $city->setMap($this);
        }

        $this->city = $city;

        return $this;
    }
}
