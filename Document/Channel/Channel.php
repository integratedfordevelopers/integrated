<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Channel;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Channel document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document(collection="channel")
 * @MongoDBUnique(fields="id")
 */
class Channel implements ChannelInterface
{
    /**
     * @var string
     * @ODM\Id(strategy="NONE")
     * @Slug(fields={"name"}, separator="_")
     */
    protected $id;

    /**
     * @var string the name of the channel
     * @Assert\NotBlank()
     * @ODM\String
     * @ODM\Index
     */
    protected $name;

    /**
     * @var array
     * @ODM\Collection
     */
    protected $domains;

    /**
     * @var string
     * @ODM\String
     */
    protected $primaryDomain;

    /**
     * @var mixed[]
     * @ODM\Hash
     */
    protected $options = [];

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $domains
     * @return $this
     */
    public function setDomains(array $domains)
    {
        $this->domains = $domains;
        return $this;
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @return \mixed[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Overrider all the option with a new set of values for this content type
     *
     * @param string[] $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = [];

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getOption($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * Set the value of the specified key.
     *
     * @param string $name
     * @param null | mixed $value
     * @return $this
     */
    public function setOption($name, $value = null)
    {
        if ($value === null) {
            unset($this->options[$name]);
        } else {
            $this->options[$name] = $value;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Get the createdAt of the channel
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the createdAt of the channel
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryDomain()
    {
        return $this->primaryDomain;
    }

    /**
     * @param string $primaryDomain
     */
    public function setPrimaryDomain($primaryDomain)
    {
        $this->primaryDomain = $primaryDomain;
    }



    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (empty($this->primaryDomain)) {
            $this->primaryDomain = reset($this->domains);
        }

        foreach ($this->domains as $domain) {

            if (!filter_var($domain, FILTER_VALIDATE_URL) || ($domain == $this->primaryDomain && substr_count($domain, '.') > 1)) {
                $context->buildViolation('You can set only primary domains!')
                    ->atPath('domains')
                    ->addViolation();

                break;
            }
        }
    }
}
