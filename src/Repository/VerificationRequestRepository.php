<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\VerificationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VerificationRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerificationRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerificationRequest[]    findAll()
 * @method VerificationRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerificationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerificationRequest::class);
    }

    /**
     * Get verification request by email and status
     *
     * @param int $status
     * @param string $email
     *
     * @return array
     */
    public function getRequestOfUserByEmailAndStatus(int $status, string $email = null): array
    {
        return $this->createQueryBuilder('vr')
            ->join('vr.owner', 'u')
            ->where('vr.status = :status')
            ->setParameter('status', $status)
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->orderBy('vr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
