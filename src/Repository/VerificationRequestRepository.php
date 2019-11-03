<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\VerificationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

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
     * Save a VerificationRequest
     *
     * @param VerificationRequest $verificationRequest
     *
     * @return VerificationRequest
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(VerificationRequest $verificationRequest): VerificationRequest
    {
        $this->getEntityManager()->persist($verificationRequest);
        $this->getEntityManager()->flush($verificationRequest);

        return $verificationRequest;
    }
}
