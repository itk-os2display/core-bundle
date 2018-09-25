<?php

namespace Os2Display\CoreBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Os2Display\MediaBundle\Entity\Media;
use Os2Display\CoreBundle\Entity\Slide;
use Os2Display\CoreBundle\Entity\Channel;

/**
 * Class CleanupService
 *
 * @package Os2Display\CoreBundle\Services
 */
class CleanupService {
  protected $entityManager;
  protected $mediaRepository;

  /**
   * CleanupService constructor.
   */
  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
    $this->mediaRepository = $entityManager->getRepository(Media::class);
  }

  /**
   * Find unused media and media with updatedAt lower than threshold timestamp.
   *
   * @param int $threshold Optional threshold. If set all items with modification
   *                       date before threshold will be included.
   * @return mixed Media.
   */
  public function findMediaToDelete($threshold = null) {
    $qb = $this->entityManager->createQueryBuilder();

    $query = $qb->select('m')
      ->from(Media::class, 'm')
      ->where('m.mediaOrders is empty');

    if (!is_null($threshold)) {
      $query->orWhere('m.updatedAt < :threshold')
      ->setParameter('threshold', \DateTime::createFromFormat('U', $threshold));
    }

    return $query->getQuery()->getResult();
  }

  /**
   * Find unused slides and slides with modifiedAt lower than threshold timestamp.
   *
   * @param int $threshold Optional threshold. If set all items with modification
   *                       date before threshold will be included.
   * @return mixed Slide.
   */
  public function findSlidesToDelete($threshold = null) {
    $qb = $this->entityManager->createQueryBuilder();

    $query = $qb->select('s')
      ->from(Slide::class, 's')
      ->where('s.channelSlideOrders is empty');

    if (!is_null($threshold)) {
      $query->orWhere('s.modifiedAt < :threshold')
        ->setParameter('threshold', $threshold);
    }

    return $query->getQuery()->getResult();
  }

  /**
   * Find unused channels and channels with modifiedAt lower than threshold timestamp.
   *
   * @param int $threshold Optional threshold. If set all items with modification
   *                       date before threshold will be included.
   * @return mixed Channel.
   */
  public function findChannelsToDelete($threshold = null) {
    $qb = $this->entityManager->createQueryBuilder();

    $query = $qb->select('c')
      ->from(Channel::class, 'c')
      ->where('c.channelScreenRegions is empty');

    if (!is_null($threshold)) {
      $query->orWhere('c.modifiedAt < :threshold')
        ->setParameter('threshold', $threshold);
    }

    return $query->getQuery()->getResult();
  }

  public function deleteEntity($entity) {
    $this->entityManager->remove($entity);
    $this->entityManager->flush();

    return true;
  }
}
