<?php
namespace App\Service;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class CustomContentSanitizer
{
    public function __construct(private HtmlSanitizerInterface $sanitizer)
    {
    }

    public function clean(string $html): string
    {
        return $this->sanitizer->sanitize($html);
    }
}