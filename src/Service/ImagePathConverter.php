<?php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImagePathConverter
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function convertToUrl(string $localPath): string
    {
        // Convert the local file path to a URL
        $publicDir = $this->router->getContext()->getBaseUrl() . '/public/';
        $relativePath = str_replace("\\", "/", substr($localPath, strpos($localPath, 'public') + 7));
        return $publicDir . $relativePath;
    }
}
