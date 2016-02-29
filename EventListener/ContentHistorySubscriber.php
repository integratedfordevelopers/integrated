<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Types\Type;

use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistorySubscriber implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function onFlush($args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        //$this->createLog($dm, $uow->getScheduledDocumentInsertions(), ContentHistory::ACTION_INSERT);
        $this->createLog($dm, $uow->getScheduledDocumentUpdates(), ContentHistory::ACTION_UPDATE);
        $this->createLog($dm, $uow->getScheduledDocumentDeletions(), ContentHistory::ACTION_DELETE);
    }

    /**
     * @param DocumentManager $dm
     * @param array $documents
     * @param string $action
     */
    protected function createLog(DocumentManager $dm, array $documents, $action)
    {
        $uow = $dm->getUnitOfWork();
        $pb = $uow->getPersistenceBuilder();

        foreach ($documents as $document) {
//            if (!$document instanceof ContentInterface) {
//                continue;
//            }

            $class = $dm->getClassMetadata(get_class($document));

            foreach ($class->getFieldNames() as $field) {

//                if ('relatedItems' == $field) {
//                    $type = Type::getType($class->fieldMappings[$field]);

              //  dump($class->fieldMappings[$field]); die;
//                dump($type); die;
//
//                    dump($type->convertToDatabaseValue($document->getRelatedItems()));
//
//                }
                dump($field);
//                $placeholders[] = (! empty($class->fieldMappings[$field]['requireSQLConversion']))
//                    ? $type->convertToDatabaseValueSQL('?', $this->platform)
            }

          //  die;

          //  dump($pb->prepareInsertData($document));
//            dump($pb->prepareUpdateData($document));
//            dump($pb->prepareUpsertData($document));

//            dump($uow->getDocumentChangeSet($document));



            $history = new ContentHistory();


            $history->setDocument(get_class($document));

            $history->setAction($action);
            $history->setChanges($pb->prepareInsertData($document));

            $dm->persist($history);
            $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($history)), $history);
        }
    }
}
