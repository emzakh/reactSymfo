<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RecettesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RecettesRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource (
 * collectionOperations={},
 * itemOperations={
 *     "get"={
            "controller"=App\Controller\Api\EmptyController::class,
 *     "read"=false,
 *     "deserialize"=false
 *     }
 * }
 * )
 * @UniqueEntity(
 *  fields={"titre"},
 *  message="titre déjà utilisé merci d'en choisir un autre"
 * )
 */

class Recettes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(min=2, max=40, minMessage="Le titre doit faire plus de 2 caractères", maxMessage="Le titre ne peut pas faire plus de 40 caractères")
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:full:commentaire"})
     */
    private $titre;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @Assert\Length(min=2, max=250, minMessage="La description doit faire plus de 2 caractères", maxMessage="La decription ne peut pas faire plus de 250 caractères")
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Assert\Length(min=20, minMessage="La recette doit faire plus de 2 caractères")
     * @ORM\Column(type="text")
     */
    private $etapes;

    /**
     * @ORM\ManyToMany(targetEntity=Produits::class, mappedBy="recettesAssociees")
     */
    private $ingredients;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="recette", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $types;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recettes")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"}, mimeTypesMessage="Vous devez upload un fichier jpg, png ou gif")
     * @Assert\File(maxSize="1024k", maxSizeMessage="Taille du fichier trop grande")
     */
    private $imgRecette;

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
            $this->slug = $slugify->slugify($this->titre);
        }
    }

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->commentaires = new ArrayCollection();

    }

    /**
     * Permet de récup le notation moyenne de ma recette
     */
    public function getAvgRatings()
    {
        // calculer la somme des notations
        // la fonction php array_reduce permet de réduire le tableau à une seule valeur (attention il faut un tableau et pas une array collection d'où l'utilisation de la méthode toArray()) - 2ème paramètre pour la fonction qui va donner chaque valeur à ajouter et le 3ème paramètre c'est la valeur par défaut
        $sum = array_reduce($this->commentaires->toArray(),function($total,$commentaire){
            return $total + $commentaire->getRating();
        },0);

        // faire la division pour avoir la moyenne (ternaire)
        if(count($this->commentaires) > 0 ) return $moyenne = round($sum / count($this->commentaires));

        return 0;
    }

    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une recette
     * @param User $author
     * @return Commentaires|null
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach($this->commentaires as $commentaire)
        {
            if($commentaire->getAuthor() === $author) return $commentaire;
        }

        return null;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }
    public function getTypes(): ?string
    {
        return $this->types;
    }

    public function setTypes(string $types): self
    {
        $this->types = $types;

        return $this;
    }
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getEtapes(): ?string
    {
        return $this->etapes;
    }

    public function setEtapes(string $etapes): self
    {
        $this->etapes = $etapes;

        return $this;
    }

    /**
     * @return Collection|Produits[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Produits $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->addRecettesAssociee($this);
        }

        return $this;
    }

    public function forEachIngredients($ingredients)
    {
        foreach($ingredients as $ingredient){
            $this->addIngredient($ingredient);
        }
    }

    public function removeIngredient(Produits $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            $ingredient->removeRecettesAssociee($this);
        }

        return $this;
    }

    /**
     * @return Collection|Commentaires[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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

    public function getImgRecette(): ?string
    {
        return $this->imgRecette;
    }

    public function setImgRecette(?string $imgRecette): self
    {
        $this->imgRecette = $imgRecette;

        return $this;
    }

    /**
     * @return Collection|Commentaires[]
     */
    public function getComments(): Collection
    {
        return $this->commentaires;
    }

    public function addComment(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeComment(Commentaires $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }

}