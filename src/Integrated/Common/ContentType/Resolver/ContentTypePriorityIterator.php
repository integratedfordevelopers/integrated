<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Resolver;

use Integrated\Common\ContentType\ContentTypeIteratorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypePriorityIterator implements ContentTypeIteratorInterface
{
    /**
     * @var \AppendIterator
     */
    private $iterator;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var bool
     */
    private $validated = false;

    /**
     * @var array
     */
    private $accepted = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->iterator = new \AppendIterator();
    }

    /**
     * This will add the types of a content type resolver to the iterator.
     *
     * The priority is set based on the order the resolvers are added to the
     * iterator. When the same resolver is appended more then ones then the
     * it will not replace the previous added instance of the resolver.
     *
     * @param ContentTypeResolverInterface $resolver
     */
    public function append(ContentTypeResolverInterface $resolver)
    {
        $this->iterator->append($resolver->getTypes());
        $this->validate();
    }

    /**
     * Preform a check to see if current content type is already accepted from an
     * other iterator
     */
    private function validate()
    {
        if ($this->validated) {
            return;
        }

        while ($this->iterator->valid()) {
            $cur = $this->current();
            $cur = json_encode(['class' => $cur->getClass(), 'type' => $cur->getType()]);

            if (!array_key_exists($cur, $this->accepted)) {
                $this->validated = true;
                $this->accepted[$cur] = $this->index;

                return;
            }

            $this->iterator->next();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (!$this->iterator->valid()) {
            return;
        }

        $this->iterator->next();

        $this->index++;
        $this->validated = false;

        $this->validate();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->valid() ? $this->index : null;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        $this->index = 0;
        $this->validated = false;
        $this->accepted = array();

        $this->validate();
    }
}
