<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: EtatPanier::class)]
    private ?EtatPanier $etat_panier = null;


    /**
     * @var Collection<int, LignePanier>
     */
    #[ORM\OneToMany(targetEntity: LignePanier::class, mappedBy: 'panier')]
    private Collection $lignesPanier;

    #[ORM\OneToOne(mappedBy: 'panier', cascade: ['persist', 'remove'])]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'paniers')]
    private ?User $user = null;





    public function __construct()
    {
        $this->lignesPanier = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtatPanier(): ?EtatPanier
    {
        return $this->etat_panier;
    }

    public function setEtatPanier(EtatPanier $etat_panier): static
    {
        $this->etat_panier = $etat_panier;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, LignePanier>
     */
    public function getLignesPanier(): Collection
    {
        return $this->lignesPanier;
    }

    public function addLignesPanier(LignePanier $lignesPanier): static
    {
        if (!$this->lignesPanier->contains($lignesPanier)) {
            $this->lignesPanier->add($lignesPanier);
            $lignesPanier->setPanier($this);
        }

        return $this;
    }

    public function removeLignesPanier(LignePanier $lignesPanier): static
    {
        if ($this->lignesPanier->removeElement($lignesPanier)) {
            // set the owning side to null (unless already changed)
            if ($lignesPanier->getPanier() === $this) {
                $lignesPanier->setPanier(null);
            }
        }

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): static
    {
        // set the owning side of the relation if necessary
        if ($commande->getPanier() !== $this) {
            $commande->setPanier($this);
        }

        $this->commande = $commande;

        return $this;
    }








}
