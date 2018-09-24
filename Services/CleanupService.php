<?php

namespace Os2Display\CoreBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Os2Display\MediaBundle\Entity\Media;

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
   * Find unused media and
   *
   * @param int $threshold Optional threshold. If set all items with modification
   *                       date before threshold will be included.
   */
  public function findMediaToDelete($threshold = null) {
    $unusedMedia = $this->mediaRepository->findBy(['mediaOrder' => null]);

    $p = 1;
  }
}
