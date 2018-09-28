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
    protected $renderedContent;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param string $value
     * @return string
     */
    public static function html($value)
    {
        return htmlspecialchars($value);
    }

    /**
     * @param array $renderData
     * @return $this
     */
    public function setRenderData(array $renderData)
    {
        $this->renderData = $renderData;
        return $this;
    }

    /**
     * @param string $viewPath
     * @return $this
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
        return $this;
    }

    /**
     * @return $this
     */
    public function render()
    {
        $data = $this->renderData;
        $data['view'] = $this;

        $this->renderedContent = static::renderView("{$this->viewPath}", $data);

        return $this;
    }

    /**
     * @return string
     */
    public function getRenderedContent()
    {
        return $this->renderedContent;
    }

    /**
     * @param string $viewPath
     * @param array $params
     *
     * @return string
     */
    public static function renderView($viewPath, array $params = [])
    {
        extract($params);

        ob_start();
        include($viewPath);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
    /**
     * @param string $url
     * @return $this
     */
    public function addJsFile($url) {
        $links = $this->jsLinks;
        $links[] = $url;

        $this->jsLinks = array_unique($links);

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function addCssFile($url) {
        $links = $this->cssLinks;
        $links[] = $url;

        $this->cssLinks = array_unique($links);

        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        if (!$this->heading) {
            $this->heading = $title;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $heading
     * @return $this
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * @param string $keywords
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
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
    public function getCachedAssetUrl($source, $siteUrl)
    {
        $href = $siteUrl . '/' . $source;
        $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $source;
        if (file_exists($path)) {
            return $href . '?' . (filemtime($path) - strtotime(date('Y') . '-01-01 00:00:00'));
        }
        return null;
    }

    /**
     * @return array
     */
    public function getCssLinks()
    {
        return $this->cssLinks;
    }

    /**
     * @return array
     */
    public function getJsLinks()
    {
        return $this->jsLinks;
    }

    /**
     * @return ContainerInterface
     */
    public function getDiContainer()
    {
        return $this->di;
    }
}
