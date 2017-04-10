<?php
/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 27/03/2017
 * Time: 17:11
 */

namespace Integrated\Bundle\ContentBundle\Services;

use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation as EmbeddedRelation;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\MongoDB\Connection;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class BulkAction
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkAction
{
    /**
     * @const the delimiter to separate action from relationId.
     */
    const BULK_DELIMITER = '::';

    /**
     * @const actions which can be executed with the BulkAction.
     */
    const BULK_ACTIONS = [
        "add" => "add",
        "remove" => "delete",
    ];

    /**
     * @const the amount of changes that can be made before flushing is invoked.
     */
    const FLUSH_PATTERN = 100;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * BulkAction constructor.
     * @param DocumentManager $dm
     * @param Connection $connection
     */
    public function __construct(DocumentManager $dm, Connection $connection)
    {
        $this->dm = $dm;
        $this->connection = $connection;
    }

    /**
     * Looks for the right className.
     * @param string $contentId
     * @param bool $devMode
     * @return string
     */
    public function getClass($contentId, $devMode = true)
    {
        // Then look if id belongs to a content.
        if ($doc = $this->connection->selectCollection('integrated', 'content')->find(['_id' => $contentId])->getSingleResult()) {
            return $doc['class'];
        }

        if (!$devMode) {
            return false;
        }

        throw new \RuntimeException('No class could be fetched in BulkAction->getClass().');
    }

    /**
     * Fetches the content with the found className.
     * @param string $contentId
     * @param bool $devMode
     * @return bool|null|object
     */
    public function getContent($contentId, $devMode = true)
    {
        // Return content if class can be fetched.
        if ($class = $this->getClass($contentId, $devMode)) {
            $content =  $this->dm->getRepository($class)->find($contentId);
            if ($content instanceof Content) {
                return $content;
            }
        }

        if (!$devMode) {
            return false;
        }

        throw new \RuntimeException('No content could be fetched in BulkAction->getContent().');
    }

    /**
     * Fetches the contents with the found classNames.
     * @param array $contentIds
     * @param bool $devMode
     * @return array
     */
    public function getContents($contentIds, $devMode = true)
    {
        $contents = [];

        // Loop trough IDs and fetch corresponding content.
        foreach ($contentIds as $contentId) {
            if ($content = $this->getContent($contentId, $devMode)) {
                $contents[] = $content;
            }
        }

        return $contents;
    }


    /**
     * Returns Relation with given relation-ID and content-type-name
     * @param string $relationId
     * @param string $sourceTypeId
     * @param bool $devMode
     * @return bool|Relation|object
     */
    public function getRelation($relationId, $sourceTypeId, $devMode = true)
    {
        $repository = $this->dm->getRepository('IntegratedContentBundle:Relation\Relation');
        $relation = $repository->findOneBy([
            '_id' => $relationId,
            'sources.$id' => $sourceTypeId
        ]);

        if ($relation instanceof Relation) {
            return $relation;
        }

        if (!$devMode) {
            return false;
        }

        throw new \RuntimeException('No Relation could be fetched in BulkAction->getRelation().');
    }

    /**
     * Fetches all Relations.
     * @return array|\Integrated\Bundle\ContentBundle\Document\Relation\Relation[]
     */
    public function getAllRelations()
    {
        $repository = $this->dm->getRepository('IntegratedContentBundle:Relation\Relation');
        $relations = $repository->findAll();

        return $relations;
    }

    /**
     * Fetches the right $relationName with or without $action in $id.
     * @param $id
     * @param bool $strToLower
     * @param bool $relNameOnly
     * @return string
     */
    public function getRelationName($id, $strToLower = false, $relNameOnly = false)
    {
        $idParts = explode(self::BULK_DELIMITER, $id);

        // Look if action exists in id and cuts it out if true.
        if (in_array(strtolower($idParts[0]), self::BULK_ACTIONS)) {
            $action = array_shift($idParts);
        }

        // Combine any ID-parts without action and fetch relation.
        $relationId = implode(self::BULK_DELIMITER, $idParts);
        $relation = $this->dm->getRepository('IntegratedContentBundle:Relation\Relation')->find($relationId);

        if ($relation instanceof Relation) {
            $relationName = $relation->getName();

            if ($strToLower) {
                $relationName = strtolower($relationName);
            }

            // If action is found in $id then paste it in the front again.
            if (!empty($action) && !$relNameOnly) {
                return implode(self::BULK_DELIMITER, [$action, $relationName]);
            }
            return $relationName;
        }

        throw new \RuntimeException('No relation-name could be fetched in BulkAction->getRelationName().');
    }

    /**
     * Returns the right name or title for reference.
     * @param string $refId
     * @return string
     */
    public function getReferenceName($refId)
    {
        $reference = $this->getContent($refId);

        if ($reference instanceof Article || $reference instanceof Taxonomy || $reference instanceof Company) {
            return $reference->getTitle();
        }
        if ($reference instanceof Person) {
            return $reference->getFirstName() . " " . $reference->getLastName();
        }

        throw new \RuntimeException('No reference-name could be fetched in BulkAction->getReferenceName().');
    }

    /**
     * Returns new array with correct reference- and relation-names
     * @param $refGroups
     * @return array
     */
    public function getReferenceNames($refGroups)
    {
        $newArray = [];

        foreach ($refGroups as $groupName => $refIds) {
            // Get the name of the relation in lowercase.
            $relationName = $this->getRelationName($groupName, true);

            $newArray[$relationName] = [];
            $references = $this->getContents($refIds);

            // Loop through all $references and return their name, title, etc. instead of their IDs.
            foreach ($references as $reference) {
                if ($reference instanceof Article || $reference instanceof Taxonomy || $reference instanceof Company) {
                    $newArray[$relationName][] = $reference->getTitle();
                }
                if ($reference instanceof Person) {
                    $newArray[$relationName][] = $reference->getFirstName() . " " . $reference->getLastName();
                }
            }
        }

        // Check if array counts as many groups as before.
        if (count($refGroups) !== count($newArray)) {
            throw new \RuntimeException('Something seemed to went wrong while creating new array in BulkAction->getReferenceNames().');
        }

        return $newArray;
    }

    /**
     * Checks if a relation corresponds to the given reference-content-type and return the object or false
     * @param string $relId
     * @param string $refContentType
     * @return bool
     */
    public function checkRefRel($relId, $refContentType)
    {
        return boolval($this->dm->getRepository('IntegratedContentBundle:Relation\Relation')->findOneBy([
            '_id' => $relId,
            'targets.$id' => $refContentType,
        ]));
    }

    /**
     * Loops trough all contentIds and refActions and executes them if possible.
     * @param $selIds
     * @param $refActions
     * @param Session $session
     * @return bool
     */
    public function execute($selIds, $refActions, Session $session)
    {
        // Keep track of successes
        $countAttempts = 0;
        $countChanges = 0;

        // Take care of flushing repeatedly.
        $flushCheck = 0;
        $flushPattern = $this::FLUSH_PATTERN;

        // Fetch all contents from selection.
        $contents = $this->getContents($selIds);

        foreach ($refActions as $actionKey => $referenceIds) {
            // Skip current loop iteration and start the next one if there are no references in the current action.
            if (empty($referenceIds)) {
                continue;
            }

            // Change $actionKey into an $action and a $relationId.
            $idParts = explode(self::BULK_DELIMITER, $actionKey);
            $action = strtolower(array_shift($idParts));
            $relationId = implode(self::BULK_DELIMITER, $idParts);

            foreach ($referenceIds as $referenceId) {
                // Track if something in process fails without a runtime error.
                $actionError = 0;
                $relationError = 0;
                $referenceError = 0;

                // Checking if more then one action is requested for current referenceID and relationID.
                foreach ($refActions as $searchKey => $searchArray) {
                    if (strpos($searchKey, $relationId) !== false && $searchKey !== $actionKey && in_array($referenceId, $searchArray)) {
                        $session->getFlashBag()->add("error", "Simultaneous action(s) found while trying to " . $action . " " . $this->getRelationName($relationId) . ": " . $this->getReferenceName($referenceId));
                        continue 2;
                    }
                }

                // fetch the reference for the contents
                $reference = $this->getContent($referenceId);

                if ($reference instanceof ContentInterface) {
                    foreach ($contents as $content) {
                        $countAttempts++;
                        if ($content instanceof Content) {
                            if ($action == "add") {
                                // Fetch existing relation if possible.
                                if ($embeddedRelation = $content->getRelation('$relationId')) {
                                    // Check if reference is a legal reference of the current relation.
                                    if ($this->checkRefRel($embeddedRelation->getRelationId(), $reference->getContentType())) {
                                        // Add reference to existing relation if possible.
                                        if ($embeddedRelation instanceof EmbeddedRelation) {
                                            $embeddedRelation->addReference($reference);
                                            $countChanges++;
                                        } else {
                                            $relationError++;
                                        }
                                    } else {
                                        $referenceError++;
                                    }
                                // If content has not an existing relation create a new one if possible.
                                } elseif ($relation = $this->getRelation($relationId, $content->getContentType(), false)) {
                                    // Check if reference is a legal reference of the current relation.
                                    if ($this->checkRefRel($relation->getId(), $reference->getContentType())) {
                                        $embeddedRelation = new EmbeddedRelation();
                                        $embeddedRelation->setRelationId($relation->getId());
                                        $embeddedRelation->setRelationType($relation->getType());
                                        $embeddedRelation->addReference($reference);

                                        // Add a new embedded relation with reference to content.
                                        $content->addRelation($embeddedRelation);
                                        $countChanges++;
                                    } else {
                                        $referenceError++;
                                    }
                                } else {
                                    $relationError++;
                                }
                            } elseif ($action == "delete") {
                                // Fetch existing relation.
                                if ($embeddedRelation = $content->getRelation($relationId)) {
                                    // Check if reference is a legal reference of the current relation.
                                    if ($this->checkRefRel($embeddedRelation->getRelationId(), $reference->getContentType())) {
                                        // Remove reference from existing relation if possible.
                                        if ($embeddedRelation instanceof EmbeddedRelation) {
                                            // Delete relation if this is the last reference.
                                            if (count($embeddedRelation->getReferences()) == 1) {
                                                $embeddedRelation->removeReference($reference);
                                                $content->removeRelation($embeddedRelation);
                                            } else {
                                                $embeddedRelation->removeReference($reference);
                                            }
                                            $countChanges++;
                                        } else {
                                            $relationError++;
                                        }
                                    } else {
                                        $referenceError++;
                                    }
                                }
                            } else {
                                $actionError++;
                            }
                        }

                        // Flush data repeatedly.
                        if ($countChanges !== 0 && $countChanges % $flushPattern == 0 && $flushCheck !== $countChanges) {
                            $this->dm->flush();
                            $flushCheck = $countChanges;
                        }
                    }
                } else {
                    $relationError++;
                }
                // Add FlashError if something went wrong.
                if ($relationError) {
                    $session->getFlashBag()->add("error", "It seems not all content(s) in the selection had " . $this->getRelationName($relationId, true) . " as a relation while trying to " . $action . ": " . $this->getReferenceName($referenceId));
                }
                if ($referenceError) {
                    $session->getFlashBag()->add("error", "It seems something went wrong with the given reference while trying to " . $action . " " . $this->getRelationName($relationId) . ": " . $this->getReferenceName($referenceId));
                }
                if ($actionError) {
                    $session->getFlashBag()->add("error", "It seems something went wrong with the action while trying to " . $action . " " . $this->getRelationName($relationId) . ": " . $this->getReferenceName($referenceId));
                }
            }
        }

        // Final flush data to database.
        $this->dm->flush();

        // Notice error with FlashMessage if something went wrong and return false.
        if ($session->getFlashBag()->has('error')) {
            $session->getFlashBag()->add("error", ($countChanges == 1 ? "$countChanges change was" : "$countChanges changes were") . " detected of the $countAttempts attempt(s).");
            $session->getFlashBag()->add("notice", "Whoops! It seems not all actions were completed. Read the error(s) below for more information.");

            if ($countChanges <= 0) {
                return false;
            }

            return true;
        }

        // Add positive FlashMessage and return true if everything went well.
        $session->getFlashBag()->add("success", "It seems all bulk actions were executed successfully! :)");

        return true;
    }

    /**
     * Convert bulkactions to string.
     * @param $refGroups
     * @param string $strGlue
     * @param string $actionGlue
     * @return string
     */
    public function actionsToString($refGroups, $actionGlue = " ", $strGlue = " and ")
    {
        $actions = [];
        $notes = [];

        foreach (self::BULK_ACTIONS as $action) {
            $actions[$action] = [];
            foreach ($refGroups as $groupName => $references) {
                if (!empty($references)) {
                    if (strpos(strtolower($groupName), $action) !== false) {
                        $actions[$action][] = $this->getRelationName($groupName, true, true);
                    }
                }
            }
        }

        foreach ($actions as $action => $relNames) {
            if (!empty($relNames)) {
                $last = array_pop($relNames);
                $notes[] = $action . $actionGlue . (!empty($relNames) ? implode(", ", $relNames) . " & " . $last : $last);
            }
        }

        $last = array_pop($notes);
        $notes = implode(", ", $notes);

        return !empty($notes) ? $notes . $strGlue . $last : $last;
    }
}
