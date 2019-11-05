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
use App\Controller\VerificationRequest\ApproveVerificationRequestAction;
use App\Controller\VerificationRequest\DeclineVerificationRequestAction;

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
 *              "security"="is_granted('ROLE_USER') and object.getOwner() == user and object.getStatus() = 0"
 *          },
 *          "approve_verifiaction_request"={
 *              "method"="PUT",
 *              "path"="verification-requests/{id}/approve",
 *              "controller"=ApproveVerificationRequestAction::class,
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "denormalization_context"={"groups"={"verifiaction_request:approve"}}
 *          },
 *          "decline_verifiaction_request"={
 *              "method"="PUT",
 *              "path"="verification-requests/{id}/decline",
 *              "controller"=DeclineVerificationRequestAction::class,
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "denormalization_context"={"groups"={"verifiaction_request:decline"}}
 *          }
 *     },
 *     normalizationContext={"groups"={"verifiaction_request:read"}},
 *     denormalizationContext={
 *          "groups"={
 *              "verifiaction_request:write",
 *              "verifiaction_request:decline",
 *              "verifiaction_request:approve"
 *          }
 *     }
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
 * @ORM\HasLifecycleCallbacks
 */
class VerificationRequest
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"verifiaction_request:read"})
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"verifiaction_request:decline"})
     */
    private $rejectionReason;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="verificationRequest", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"verifiaction_request:read"})
     * @Gedmo\Blameable(on="create")
     */
    private $owner;

    public function __construct()
    {
        $this->status = VerificationStatusEnum::VERIFICATION_REQUESTED;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return MediaObject|null
     */
    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    /**
     * @param MediaObject $image
     *
     * @return $this
     */
    public function setImage(MediaObject $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return $this
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    /**
     * @param string|null $rejectionReason
     *
     * @return $this
     */
    public function setRejectionReason(?string $rejectionReason): self
    {
        $this->rejectionReason = $rejectionReason;

        return $this;
    }
}
