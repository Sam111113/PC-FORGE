<?php
namespace App\Vich;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

/**
 * Détermine dynamiquement le sous-répertoire d'upload pour VichUploaderBundle.
 *
 * Cette classe permet d'organiser les fichiers uploadés dans des sous-dossiers
 * basés sur le contexte de l'entité (ex: "news", "build", "user").
 * Si l'entité possède une méthode getContext(), le fichier sera stocké
 * dans le répertoire correspondant, sinon dans "others".
 */
class EntityDirectoryNamer implements DirectoryNamerInterface
{
    /**
     * Retourne le nom du sous-répertoire où stocker le fichier uploadé.
     *
     * @param object $object L'entité contenant le fichier à uploader
     * @param PropertyMapping $mapping La configuration du mapping Vich
     * @return string Le nom du sous-répertoire (ex: "news", "build", "others")
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        // Vérifie si l'entité définit un contexte pour organiser les uploads
        if (method_exists($object, 'getContext') && $object->getContext()) {
            return $object->getContext();
        }

        // Répertoire par défaut si aucun contexte n'est défini
        return 'others';
    }
}