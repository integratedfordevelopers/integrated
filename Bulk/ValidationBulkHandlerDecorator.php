<?php
//
// /**
//  * This file is part of the Integrated package.
//  *
//  * (c) e-Active B.V. <integrated@e-active.nl>
//  *
//  * For the full copyright and license information, please view the LICENSE
//  * file that was distributed with this source code.
//  */
//
// namespace Integrated\Bundle\ContentBundle\Bulk;
//
// use Doctrine\Common\Collections\Collection;
// use Integrated\Bundle\ContentBundle\Bulk\Action\ActionInterface;
// use Integrated\Common\Content\ContentInterface;
//
// /**
//  * @author Patrick Mestebeld <patrick@e-active.nl>
//  */
// class ValidationBulkHandlerDecorator implements BulkHandlerInterface
// {
//     /**
//      * @var BulkHandlerInterface
//      */
//     private $decorated;
//
//     /**
//      * ValidateBulkHandlerDecorator constructor.
//      * @param BulkHandlerInterface $decorated
//      */
//     public function __construct(BulkHandlerInterface $decorated)
//     {
//         $this->decorated = $decorated;
//     }
//
//     /**
//      * @param Collection $contents
//      * @param Collection $actions
//      * @return $this
//      */
//     public function execute(Collection $contents, Collection $actions)
//     {
//         $contents = $this->filterCollection($contents, ContentInterface::class);
//         $actions = $this->filterCollection($actions, ActionInterface::class);
//
//         if (!$contents->count()) {
//             new \RuntimeException('No content of ' . ContentInterface::class . ' was found');
//         }
//
//         if (!$actions->count()) {
//             new \RuntimeException('No action of ' . ActionInterface::class . ' was found');
//         }
//
//         $this->decorated->execute($contents, $actions);
//
//         return $this;
//     }
//
//     /**
//      * @param Collection $collection
//      * @param $class
//      * @return Collection
//      */
//     private function filterCollection(Collection $collection, $class)
//     {
//         return $collection->filter(function ($element) use ($class) {
//             return $element instanceof $class;
//         });
//     }
// }
