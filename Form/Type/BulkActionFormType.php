<?php
/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 28/03/2017
 * Time: 15:58
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Services\BulkAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BulkActionFormType extends AbstractType
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var BulkAction
     */
    protected $bulkAction;

    /**
     * BulkActionFormType constructor.
     * @param DocumentManager $dm
     * @param BulkAction $bulkAction
     */
    public function __construct(DocumentManager $dm, BulkAction $bulkAction)
    {
        $this->dm = $dm;
        $this->bulkAction = $bulkAction;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bulkAction = $this->bulkAction;

        foreach ($options['relations'] as $relation) {
            if ($relation instanceof Relation) {
                $contentTypes = [];

                // Fetch all content-types of relation.
                foreach ($relation->getTargets() as $contentType) {
                    $contentTypes[] = [
                        'type' => $contentType->getType(),
                        'name' => $contentType->getName(),
                    ];
                }

                // Create a field for each action per relation.
                foreach ($bulkAction::BULK_ACTIONS as $action) {
                    $actionId = $action . $bulkAction::BULK_DELIMITER . $relation->getId();
                    $label = ucfirst($action) . " " . strtolower($relation->getName());

                    $builder->add($actionId, ChoiceType::class, [
                        'label' => $label,
                        'choices' => [],
                        'multiple' => true,
                        'placeholder' => 'Choose an option',
                        'required' => false,
                        'attr' => [
                            'class' => 'relation-items integrated_select2',
                            'data-multiple' => 1,
                            'data-id' => $relation->getId(),
                            'data-types' => json_encode($contentTypes),
                        ],
                    ]);

                    // Prevent form from erasing choiceinputs with javascript.
                    $builder->get($actionId)->resetViewTransformers();
                }
            }
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

            $form = $event->getForm();
            $data = $event->getData();

            if (empty($data)) {
                $form->addError(new FormError("Did you forgot to make a choice?"));
                return;
            }

            $newData = $this->dataValidation($form, $data);

            $event->setData($newData);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'relations' => '',
            'validation_groups' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'intgrated_content_bulk_edit';
    }

    /**
     * Checks if the given data is valid and does not give a conflict during the bulkaction.
     * @param FormInterface $form
     * @param $data
     * @return array|null
     */
    protected function dataValidation(FormInterface $form, $data)
    {
        $bulkAction = $this->bulkAction;
        $newData = [];

        foreach ($data as $fieldName => $fieldData) {
            // Check $fieldName.
            $nameParts = explode($bulkAction::BULK_DELIMITER, $fieldName);
            $action = array_shift($nameParts);
            $relationId = implode($bulkAction::BULK_DELIMITER, $nameParts);

            if (!in_array($action, $bulkAction::BULK_ACTIONS)) {
                $form->addError(new FormError("Incorrect action found."));
                return null;
            }

            $relation = $this->dm->getRepository('IntegratedContentBundle:Relation\Relation')->find($relationId);

            if (empty($relation)) {
                $form->addError(new FormError("Incorrect relation found."));
                return null;
            }

            $newFieldData = [];

            foreach ($fieldData as $referenceId) {
                // Check if reference concerns a content and if reference is a legal reference of the current relation.
                $reference = $bulkAction->getContent($referenceId, false);
                if (!$reference instanceof Content || !$bulkAction->checkRefRel($relation->getId(), $reference->getContentType())) {
                    $form->addError(new FormError("Incorrect reference found."));
                    return null;
                }

                // Check if a Simultaneous action with the same reference and relation can be found.
                foreach ($data as $searchKey => $searchArray) {
                    if (strpos($searchKey, $relationId) !== false && $searchKey !== $fieldName && in_array($referenceId, $searchArray)) {
                        $form->addError(new FormError("Simultaneous actions found for reference: " . $reference->getId()));
                        return null;
                    }
                }

                $newFieldData[] = $reference->getId();
            }

            // Add new fieldData if not empty.
            if (!empty($newFieldData)) {
                $newData[$fieldName] = $newFieldData;
            }
        }

        return $newData;
    }
}
