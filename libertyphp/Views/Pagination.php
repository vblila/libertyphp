<?php

namespace Libertyphp\Views;

class Pagination
{
    /** @var int */
    protected $count;

    /** @var int */
    protected $currentPage;

    /** @var int */
    protected $pageLimit;

    /** @var int */
    protected $baseUrl;

    /** @var string */
    protected $pageParameter;

    public function __construct($count, $currentPage, $pageLimit, $baseUrl = '', $pageParameter = 'page')
    {
        $this->count = $count;
        $this->currentPage = $currentPage;
        $this->pageLimit = $pageLimit;
        $this->baseUrl = $baseUrl;
        $this->pageParameter = $pageParameter;
    }

    /**
     * @return int[]
     */
    public function getPages()
    {
        $pages = [];

        $pages[] = 1;

        $innerFirstPage = max($this->currentPage - 4, 2);
        $innerLastPage = min($this->currentPage + 4, $this->getLastPage() - 1);

        if ($innerFirstPage > 2) {
            $pages[] = null;
        }

        for ($p = $innerFirstPage; $p <= $innerLastPage; $p++) {
            $pages[] = $p;
        }

        if ($innerLastPage > 1) {
            if ($innerLastPage < $this->getLastPage() - 1) {
                $pages[] = null;
            }

            $pages[] = $this->getLastPage();
        }

        return $pages;
    }

    /**
     * @return int
     */
    public function getLastPage()
    {
        return ceil($this->count / $this->pageLimit);
    }

    /**
     * @param $page
     * @return string
     */
    public function getPageUrl($page)
    {
        $parsedUrl = parse_url($this->baseUrl);

        $url = empty($parsedUrl['query'])
            ? "{$this->baseUrl}?{$this->pageParameter}={$page}"
            : "{$this->baseUrl}&{$this->pageParameter}={$page}";

        return $url;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getPageLimit()
    {
        return $this->pageLimit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->pageLimit * ($this->currentPage - 1);
    }
}
