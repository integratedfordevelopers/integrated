<?php

namespace Integrated\Bundle\FormTypeBundle\Form\Extension;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Relation\HtmlRelation;
use Integrated\Bundle\FormTypeBundle\EventListener\EditorImageRelationEventListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class EditorImageRelationExtension extends AbstractTypeExtension
{
    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @var HtmlRelation
     */
    private $parser;

    public function __construct(DocumentManager $manager, HtmlRelation $parser = null)
    {
        $this->manager = $manager;
        $this->parser = $parser ?: new HtmlRelation();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new EditorImageRelationEventListener($this->manager, $this->parser));
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
