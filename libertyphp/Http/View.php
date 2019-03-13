<?php

namespace Libertyphp\Http;

use Psr\Container\ContainerInterface;

class View
{
    /** @var ContainerInterface */
    protected $di;

    /** @var string */
    protected $viewPath;

    /** @var array */
    protected $jsLinks = [];

    /** @var array */
    protected $cssLinks = [];

    /** @var string */
    protected $title;

    /** @var string */
    protected $heading;

    /** @var string */
    protected $keywords;

    /** @var string */
    protected $description;

    /** @var array */
    protected $renderData = [];

    /** @var string */
    protected $renderedContent = '';

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public static function html(string $value): string
    {
        return htmlspecialchars($value);
    }

    public function setRenderData(array $renderData): View
    {
        $this->renderData = $renderData;
        return $this;
    }

    public function setViewPath(string $viewPath): View
    {
        $this->viewPath = $viewPath;
        return $this;
    }

    /**
     * Формирует renderedContent
     * @return $this
     */
    public function render(): View
    {
        $data = $this->renderData;
        $data['view'] = $this;

        $this->renderedContent = static::renderView("{$this->viewPath}", $data);

        return $this;
    }

    public function getRenderedContent(): string
    {
        return $this->renderedContent;
    }

    public static function renderView(string $viewPath, array $params = []): string
    {
        extract($params);

        ob_start();
        include($viewPath);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public function addJsFile(string $url): View
    {
        $links = $this->jsLinks;
        $links[] = $url;

        $this->jsLinks = array_unique($links);

        return $this;
    }

    public function addCssFile(string $url): View
    {
        $links = $this->cssLinks;
        $links[] = $url;

        $this->cssLinks = array_unique($links);

        return $this;
    }

    public function setTitle(string $title): View
    {
        $this->title = $title;

        if (!$this->heading) {
            $this->heading = $title;
        }

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setHeading(string $heading): View
    {
        $this->heading = $heading;
        return $this;
    }

    public function getHeading(): string
    {
        return $this->heading;
    }

    public function setKeywords(string $keywords): View
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setDescription(string $description): View
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Возвращает URL к файлу с учетом идентификатора кеша файла (?id)
     *
     * @param string $source
     * @param string $siteUrl
     *
     * @return string|null
     */
    public function getCachedAssetUrl(string $source, string $siteUrl): ?string
    {
        $href = $siteUrl . '/' . $source;
        $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $source;
        if (file_exists($path)) {
            return $href . '?' . (filemtime($path) - strtotime(date('Y') . '-01-01 00:00:00'));
        }

        return null;
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

    public function getDiContainer(): ContainerInterface
    {
        return $this->di;
    }
}
