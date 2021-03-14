<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProgrammeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProgrammeRepository::class)
 */
class Programme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sessions::class, inversedBy="programmes")
     */
    private $sessions;

    /**
     * @ORM\ManyToOne(targetEntity=Modules::class, inversedBy="programme")
     */
    private $modules;

    /**
     * @ORM\Column(type="integer")
     *      @Assert\GreaterThan(0)
     */
    private $jours;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessions(): ?Sessions
    {
        return $this->sessions;
    }

    public function setSessions(?Sessions $sessions): self
    {
        $this->sessions = $sessions;

        return $this;
    }

    public function getModules(): ?Modules
    {
        return $this->modules;
    }

    public function setModules(?Modules $modules): self
    {
        $this->modules = $modules;

        return $this;
    }


    public function getJours(): ?int
    {
        return $this->jours;
    }

    public function setJours(?int $jours): self
    {
        $this->jours = $jours;

        return $this;
    }
}
