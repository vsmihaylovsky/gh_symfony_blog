<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 6:37 PM
 */

namespace AppBundle\EventListener;
use AppBundle\Entity\Author;
use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Comment;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }

    /**
     * Action to happen after persisting an entity
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $now = time();
        $entity = $args->getEntity();

        if($entity instanceof Comment) {
            $entity->setCreatedAt($now);
        }

        if($entity instanceof Author) {
            $entity->setSlug($this->getSlug($entity->getName()));
        }

        if($entity instanceof Article) {
            $entity->setCreatedAt($now);
            $entity->setUpdatedAt($now);
            $entity->setSlug($this->getSlug($entity->getHeader()));
        }

        if($entity instanceof Tag) {
            $entity->setSlug($this->getSlug($entity->getName()));
        }
    }

    /**
     * Action to happen after persisting an entity
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Author) {
            $entity->setSlug($this->getSlug($entity->getName()));
        }

        if($entity instanceof Article) {
            $now = time();
            $entity->setUpdatedAt($now);
            $entity->setSlug($this->getSlug($entity->getHeader()));
        }

        if($entity instanceof Tag) {
            $entity->setSlug($this->getSlug($entity->getName()));
        }
    }

    private function getSlug($title)
    {
        return str_replace(' ', '_', strtolower($title));
    }
}