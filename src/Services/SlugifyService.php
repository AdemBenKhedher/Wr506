<?php

namespace App\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SlugifyService
{
    public function slugify(string $text): string
    {
        // Convertir en minuscule
        $text = strtolower($text);
        
        // Remplacer les caractères non alphanumériques par des tirets
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        
        // Supprimer les tirets au début et à la fin
        $text = trim($text, '-');
        
        return $text;
    }
}
