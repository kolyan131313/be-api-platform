<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_USER')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_BLOGGER')"
 *          }
 *     },
 *     itemOperations={
 *          "put"={
 *              "security"="is_granted('ROLE_BLOGGER')"
 *          },
 *          "get"={
 *              "security"="is_granted('ROLE_USER')"
 *          }
 *     },
 *     normalizationContext={"groups"={"post:read"}},
 *     denormalizationContext={"groups"={"post:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post:read", "post:write"})
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255, maxMessage="Title length more than 255 chars")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post:read", "post:write"})
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Blameable(on="create")
     */
    private $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
