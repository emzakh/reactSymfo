<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Cocur\Slugify\Slugify;
use App\Repository\ProduitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProduitsRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"produits_read"}
 *     }
 * )
 * @UniqueEntity(
 *  fields={"nom"},
 *  message="Un autre produit possède déjà ce nom, merci de le modifier"
 * )
 */

class Produits
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"produits_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"produits_read"})
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"}, mimeTypesMessage="Vous devez upload un fichier jpg, png ou gif")
     * @Assert\File(maxSize="1024k", maxSizeMessage="Taille du fichier trop grande")
     * @Groups({"produits_read"})
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=2, max=50, minMessage="Le nom du produit doit faire plus de 2 caractères", maxMessage="Le nom ne peut pas faire plus de 50 caractères")
     * @Groups({"produits_read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=2, max=50, minMessage="Le nom latin du produit doit faire plus de 2 caractères", maxMessage="Le nom ne peut pas faire plus de 50 caractères")
     * @Groups({"produits_read"})
     */
    private $nomlatin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=2, minMessage="Les effets doivent faire plus de 2 caractères")
     * @Groups({"produits_read"})
     */
    private $effets;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=20, minMessage="Votre description doit faire plus de 10 caractères")
     * @Groups({"produits_read"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Recettes::class, inversedBy="ingredients")
     * @Groups({"produits_read"})
     */
    private $recettesAssociees;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $slug;


    /**
     * Permet d'initialiser le slug automatiquement s'il n'est pas fourni
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function initializeSlug(){
        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->nom);
        }
    }


    public function __construct()
    {
        $this->recettesAssociees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNomlatin(): ?string
    {
        return $this->nomlatin;
    }

    public function setNomlatin(?string $nomlatin): self
    {
        $this->nomlatin = $nomlatin;

        return $this;
    }

    public function getEffets(): ?string
    {
        return $this->effets;
    }

    public function setEffets(string $effets): self
    {
        $this->effets = $effets;

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
     * @return Collection|Recettes[]
     */
    public function getRecettesAssociees(): Collection
    {
        return $this->recettesAssociees;
    }

    public function addRecettesAssociee(Recettes $recettesAssociee): self
    {
        if (!$this->recettesAssociees->contains($recettesAssociee)) {
            $this->recettesAssociees[] = $recettesAssociee;
        }

        return $this;
    }

    public function removeRecettesAssociee(Recettes $recettesAssociee): self
    {
        $this->recettesAssociees->removeElement($recettesAssociee);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
