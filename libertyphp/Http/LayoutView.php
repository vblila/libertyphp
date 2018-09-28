<?php

namespace Libertyphp\Http;

class LayoutView extends View
{
    /** @var View */
    protected $contentView;

    /**
     * @param View $contentView
     * @return $this
     */
    public function setContentView(View $contentView)
    {
        $this->contentView = $contentView;
        $this->title = $contentView->getTitle();
        $this->description = $contentView->getDescription();
        $this->heading = $contentView->getHeading();
        $this->keywords = $contentView->getKeywords();

        return $this;
    }

    public function getCssLinks()
    {
        $layoutViewCssLinks = $this->cssLinks;
        $contentViewCssLinks = $this->contentView->getCssLinks();

        $mergedCssLinks = array_unique(array_merge($layoutViewCssLinks, $contentViewCssLinks));

        return $mergedCssLinks;
    }

    public function getJsLinks()
    {
        $layoutViewJsLinks = $this->jsLinks;
        $contentViewJsLinks = $this->contentView->getJsLinks();

        $mergedJsLinks = array_unique(array_merge($layoutViewJsLinks, $contentViewJsLinks));

        return $mergedJsLinks;
    }

    public function render()
    {
        $layoutData = $this->renderData;

        $layoutData['view'] = $this;
        $layoutData['content'] = $this->contentView->getRenderedContent() ?? $this->contentView->render()->getRenderedContent();

        $this->renderedContent = static::renderView("{$this->viewPath}", $layoutData);

        return $this;
    }
}
