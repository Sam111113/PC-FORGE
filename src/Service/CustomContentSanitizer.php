<?php
namespace App\Service;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

/**
 * Service de nettoyage du contenu HTML pour prévenir les attaques XSS.
 *
 * Utilise le composant HtmlSanitizer de Symfony pour filtrer le HTML
 * et ne conserver que les balises et attributs autorisés.
 * Utilisé principalement pour le contenu des news et autres champs rich-text.
 */
class CustomContentSanitizer
{
    /**
     * @param HtmlSanitizerInterface $sanitizer Le service de sanitization Symfony
     */
    public function __construct(private HtmlSanitizerInterface $sanitizer)
    {
    }

    /**
     * Nettoie une chaîne HTML en supprimant les éléments potentiellement dangereux.
     *
     * @param string $html Le contenu HTML brut à nettoyer
     * @return string Le HTML nettoyé et sécurisé
     */
    public function clean(string $html): string
    {
        return $this->sanitizer->sanitize($html);
    }
}