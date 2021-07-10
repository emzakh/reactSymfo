<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *  fields={"email"},
 *  message="email déjà utilisé merci d'en choisir un autre"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:commentaire"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message="Veuillez renseigner une adresse email valide",groups={"Registration"})
     * @Assert\NotBlank(groups={"Registration"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"Registration"})
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas correctement confirmé votre mot de passe",groups={"Registration"})
     *
     */
    private $passwordConfirm;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="author", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity=Recettes::class, mappedBy="author", cascade={"remove"})
     */
    private $recettes;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom")
     * @Groups({"read:commentaire"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="Vous devez renseigner votre nom")
     * @Groups({"read:commentaire"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"}, mimeTypesMessage="Vous devez upload un fichier jpg, png ou gif")
     * @Assert\File(maxSize="1024k", maxSizeMessage="Taille du fichier trop grande")
     */
    private $picture;



    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min=10, minMessage="Votre presentation doit faire au minimum 10 caractères")
     */
    private $presentation;

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
            $this->slug = $slugify->slugify($this->firstName.' '.$this->lastName.' '.rand());
        }
    }

    public function getFullName(){
        return "{$this->firstName} {$this->lastName}";
    }


    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $commentaire->setAuthor($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getAuthor() === $this) {
                $commentaire->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Recettes[]
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recettes $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes[] = $recette;
            $recette->setAuthor($this);
        }

        return $this;
    }

    public function removeRecette(Recettes $recette): self
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getAuthor() === $this) {
                $recette->setAuthor(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * toString
     * @return string
     */
    public function __toString(){
        return $this->getFullName();
    }



}
