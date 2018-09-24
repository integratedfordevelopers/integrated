<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\Type;

use ArrayObject;
use Integrated\Bundle\ContentBundle\Form\Util\FormUtil;
use Integrated\Bundle\StorageBundle\Form\EventListener\FileEventSubscriber;
use Integrated\Bundle\StorageBundle\Form\Upload\StorageIntentUpload;
use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\ManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FileType extends AbstractType
{
    /**
     * @var AppCache
     */
    private $appCache;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(AppCache $appCache)
    {
        $this->appCache = $appCache;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // The field might not be required in the integrated content type
        $resolver->setDefaults([
            'compound' => true,
            'required' => false,
            'constraints_file' => [],
        ]);

        // Move the constraints from the main object to the file object
        $constraints = new ArrayObject();
        $resolver->setNormalizer(
            'constraints',
            function (Options $options, $value) use ($constraints) {
                $constraints->exchangeArray(\is_object($value) ? [$value] : (array) $value);

                return [];
            }
        );
        $resolver->setNormalizer(
            'constraints_file',
            function (Options $options) use ($constraints) {
                return $constraints->getArrayCopy();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', SymfonyFileType::class, [
            'required' => false,
            'mapped' => false,
            'empty_data' => null,
            'constraints' => $options['constraints_file'],
        ]);

        $builder->add('remove', CheckboxType::class, [
            'mapped' => false,
            'required' => false,
        ]);

        $builder->addEventSubscriber(new FileEventSubscriber($this->appCache));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_file';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();

        if ($data instanceof StorageIntentUpload) {
            $view->vars['preview'] = $data;

            if (!FormUtil::getRootForm($form)->isValid()) {
                $view->vars['preview'] = $data->getOriginal();
            }
        } elseif ($data instanceof StorageInterface) {
            $view->vars['preview'] = $data;
        }
    }
}
