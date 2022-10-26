<?php

namespace Libertyphp\Views;

use Exception;

class LayoutView extends View
{
    protected string $title = '';

    protected string $keywords = '';

    protected string $description = '';

    protected ?View $contentView = null;

    public function setContentView(View $contentView): LayoutView
    {
        $this->contentView = $contentView;
        return $this;
    }

    public function getContentView(): ?View
    {
        return $this->contentView;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setKeywords(string $keywords): static
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function getCssLinks(): array
    {
        return array_merge($this->cssLinks, $this->contentView->getCssLinks());
    }

    /**
     * @return string[]
     */
    public function getJsLinks(): array
    {
        return array_merge($this->jsLinks, $this->contentView->getJsLinks());
    }

    public function render(): static
    {
        if (!$this->contentView) {
            throw new Exception('Content view must be set');
        }

        $this->contentView->render();

        return parent::render();
    }
}
