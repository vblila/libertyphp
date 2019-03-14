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

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $pageParameter;

    public function __construct(
        int $count,
        int $currentPage,
        int $pageLimit,
        string $baseUrl = '',
        string $pageParameter = 'page'
    ) {
        $this->count = $count;
        $this->currentPage = $currentPage;
        $this->pageLimit = $pageLimit;
        $this->baseUrl = $baseUrl;
        $this->pageParameter = $pageParameter;
    }

    /**
     * @return int[]
     */
    public function getPages(): array
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

        if ($innerLastPage >= 1) {
            if ($innerLastPage < $this->getLastPage() - 1) {
                $pages[] = null;
            }

            $pages[] = $this->getLastPage();
        }

        return $pages;
    }

    public function getLastPage(): int
    {
        return ceil($this->count / $this->pageLimit);
    }

    public function getPageUrl(int $page): string
    {
        $parsedUrl = parse_url($this->baseUrl);

        $url = empty($parsedUrl['query'])
            ? "{$this->baseUrl}?{$this->pageParameter}={$page}"
            : "{$this->baseUrl}&{$this->pageParameter}={$page}";

        return $url;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPageLimit(): int
    {
        return $this->pageLimit;
    }

    public function getOffset(): int
    {
        return $this->pageLimit * ($this->currentPage - 1);
    }
}
