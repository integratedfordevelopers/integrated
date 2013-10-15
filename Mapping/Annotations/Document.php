<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Annotations;

/**
 * Annotation for defining metadata for a document
 *
 * @package Integrated\Bundle\ContentBundle\Mapping\Annotations
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @Annotation
 */
class Document
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor
     *
     * @param array $data
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $data['name'] = $data['value'];
            unset($data['value']);
        }

        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

    /**
     * Get the name of the document
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the document
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}