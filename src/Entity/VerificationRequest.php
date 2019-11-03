<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Enum\VerificationStatusEnum;
use App\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "path"="/verification-requests",
 *              "security"="is_granted('ROLE_ADMIN')"
 *          },
 *          "post"={
 *              "path"="/verification-requests",
 *              "security"="is_granted('ROLE_USER')"
 *          }
 *     },
 *     itemOperations={
 *          "put"={
 *              "path"="/verification-requests/{id}",
 *              "security"="is_granted('ROLE_USER') and object.getOwner() == user",
 *          },
 *          "get"={
 *              "path"="/verification-requests/{id}",
 *              "security"="is_granted('ROLE_USER') and object.getOwner() == user"
 *          },
 *          "approve_verifiaction_reuest"={
 *              "method"="PUT",
 *              "path"="verification-requests/{id}/approve",
 *              "controller"=ApproveVerificationRequestAction::class,
 *              "denormalization_context"={"groups"={"verifiaction_request:approve"}},
 *              "swagger_context" = {
 *                 "parameters" = {
 *                     {
 *                         "name" = "email",
 *                         "in" = "path",
 *                         "required" = "true",
 *                         "type" = "string"
 *                     }
 *                 },
 *              }
 *          }
 *     },
 *     normalizationContext={"groups"={"verifiaction_request:read"}},
 *     denormalizationContext={"groups"={"verifiaction_request:write"}}
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\VerificationRequestRepository")
 * @ApiFilter(NumericFilter::class, properties={"status"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "owner": "exact",
 *     "owner.email": "partial",
 *     "owner.firstName": "partial",
 *     "owner.lastName": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"createdAt"})
 */
class VerificationRequest
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"verifiaction_request:read"})
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * @var MediaObject|null
     *
     * @ORM\ManyToOne(targetEntity=MediaObject::class)
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     * @Groups({"verifiaction_request:read", "verifiaction_request:write"})
     */
    public $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"verifiaction_request:read", "verifiaction_request:write"})
     */
    private $message;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="verificationRequest", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"verifiaction_request:read"})
     * @Gedmo\Blameable(on="create")
     */
    private $owner;

    public function __construct()
    {
        $this->status = VerificationStatusEnum::REQUESTED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    public function setImage(MediaObject $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
