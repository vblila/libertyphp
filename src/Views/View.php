<?php

namespace Libertyphp\Views;

use Exception;

class View
{
    protected ?string $viewPath = null;

    protected ?string $renderedContent = null;

    protected array $renderData = [];

    /** @var string[] */
    protected array $jsLinks = [];

    /** @var string[] */
    protected array $cssLinks = [];

    public static function html(?string $value): string
    {
        return htmlspecialchars($value ?? '');
    }

    public function setViewPath(string $viewPath): static
    {
        $this->viewPath = $viewPath;
        return $this;
    }

    public function setRenderData(array $renderData): static
    {
        $this->renderData = $renderData;
        return $this;
    }

    /**
     * Generate renderedContent
     * @throws Exception
     */
    public function render(): static
    {
        if (!$this->viewPath) {
            throw new Exception('View path must be set');
        }

        $data = $this->renderData;
        $data['view'] = $this;

        $this->renderedContent = Content::render("{$this->viewPath}", $data);

        return $this;
    }

    public function getRenderedContent(): ?string
    {
        return $this->renderedContent;
    }

    public function addJsFile(string $url): static
    {
        $this->jsLinks[md5($url)] = $url;
        return $this;
    }

    public function addCssFile(string $url): static
    {
        $this->cssLinks[md5($url)] = $url;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getCssLinks(): array
    {
        return $this->cssLinks;
    }

    /**
     * @return string[]
     */
    public function getJsLinks(): array
    {
        return $this->jsLinks;
    }

    public static function getCachedAssetUrl(string $source, string $documentRoot, string $siteUrl = ''): ?string
    {
        $href = $siteUrl . '/' . $source;
        $path = $documentRoot . '/' . $source;
        if (file_exists($path)) {
            return $href . '?' . (filemtime($path) - strtotime(date('Y') . '-01-01 00:00:00'));
        }

        return null;
    }
}
