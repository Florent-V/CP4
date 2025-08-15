<?php

namespace App\Twig;

use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ShareCodeExtension extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('share_code_url', [$this, 'generateShareCodeUrl']),
        ];
    }

    /**
     * Génère l'URL de partage pour n'importe quelle entité
     * Usage dans Twig: {{ share_code_url(splitter) }}
     */
    public function generateShareCodeUrl(object $entity): string
    {
        $entityClass = get_class($entity);

        // Extraire le nom court de la classe (App\Entity\Splitter -> splitter)
        $shortName = strtolower(basename(str_replace('\\', '/', $entityClass)));
        $entityId = $this->getEntityId($entity);

        return $this->urlGenerator->generate('api_share_code', [
            'entityType' => $shortName,
            'entityId' => $entityId
        ]);
    }

    /**
     * Récupère l'ID d'une entité
     */
    private function getEntityId(object $entity): string
    {
        if (!method_exists($entity, 'getId')) {
            throw new InvalidArgumentException('L\'entité doit avoir une méthode getId()');
        }

        $id = $entity->getId();

        if ($id === null) {
            throw new InvalidArgumentException('L\'entité doit avoir un ID non null');
        }

        return (string) $id;
    }
}
