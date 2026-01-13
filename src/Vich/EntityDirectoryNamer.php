<?php
namespace App\Vich;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class EntityDirectoryNamer implements DirectoryNamerInterface
{
    public function directoryName($object, PropertyMapping $mapping): string
    {
        if (method_exists($object, 'getContext') && $object->getContext()) {
            return $object->getContext();
        }

        return 'others';
    }
}