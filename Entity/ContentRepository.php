<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    public function libraryContentCount()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('l.machineName, l.majorVersion, l.minorVersion, count(l.id)')
            ->join('c.library', 'l')
            ->groupBy('l.machineName, l.majorVersion, l.minorVersion');
        return $qb->getQuery()->getArrayResult();
    }
    public function countContent()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c)');
        return $qb->getQuery()->getSingleScalarResult();
    }
    public function countNotFiltered()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c)')
            ->where('c.library is not null and c.filteredParameters is null');
        return $qb->getQuery()->getSingleScalarResult();
    }
    public function countLibraryContent($libraryId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c)')
            ->where('c.library = :library')
            ->setParameter('library', $libraryId);
        return $qb->getQuery()->getSingleScalarResult();
    }
    /**
     * @param $userId
     * @param Content $content
     * @return ContentResult|null
     */
    public function findUserResult($userId, Content $content)
    {
        $contentResultRepo = $this->getEntityManager()->getRepository('Emmedy\H5PBundle\Entity\ContentResult');
        $response = $contentResultRepo->createQueryBuilder('cr')
            ->where('cr.userId = :userId')
            ->andWhere('cr.content = :content')
            ->setParameter('userId', $userId)
            ->setParameter('content', $content)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        if (empty($response)) {
            return null;
        }
        return $response[0];
    }
}
