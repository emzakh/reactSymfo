<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentairesRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Api\CommentCreateController;

/**
 * @ORM\Entity(repositoryClass=CommentairesRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     attributes={
 *     "order"={"createdAt":"DESC"}
 *     },
 *     paginationItemsPerPage=4,
 *     normalizationContext={"groups"={"read:commentaire"}}, *
 *     collectionOperations={
 *     "get",
 *     "post"={
 *      "security"="is_granted('IS_AUTHENTICATED_FULLY')",
 *      "controller"=App\Controller\Api\CommentCreateController::class,
 *      "denormalization_context"={"groups"={"create:commentaire"}}
 *     }
 * },
 * itemOperations={
 *     "get"={
            "normalization_context"={"groups"={"read:commentaire", "read:full:commentaire"}}
 *     },
 *     "put"={
 *      "security"="is_granted('EDIT_COMMENT',object)",
 *      *      "denormalization_context"={"groups"={"update:commentaire"}}

 *     },
 *      "delete"={
 *      "security"="is_granted('EDIT_COMMENT',object)",
 *     }
 * }
 * )
 * @ApiFilter(SearchFilter::class,
 *     properties={"recette": "exact"})
 */
class Commentaires
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:commentaire"})
     */
    private $id;

    /**
     * @Assert\Length(min=2, max=500, minMessage="Le commentaire doit faire plus de 2 caractères", maxMessage="Le commentaire ne peut pas faire plus de 500 caractères")
     * @ORM\Column(type="text")
     * @Groups({"read:commentaire", "create:commentaire", "update:commentaire"})
     */
    private $contenu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:full:commentaire"})
     */
    private $rating;

    /**
     * @ORM\ManyToOne(targetEntity=Recettes::class, inversedBy="commentaires")
     * @Groups({"read:full:commentaire","create:commentaire"})
     */
    private $recette;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentaires")
     * @Groups({"read:commentaire"})
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:commentaire"})
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     */

    public function prePersist()
    {
        if(empty($this->createdAt))
        {
            $this->createdAt = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getRecette(): ?Recettes
    {
        return $this->recette;
    }

    public function setRecette(?Recettes $recette): self
    {
        $this->recette = $recette;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


}