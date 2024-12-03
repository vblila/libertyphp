<?php declare(strict_types=1);

namespace Libertyphp\Tests\Views;

use Libertyphp\Views\Pagination;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    public function testGeneralCasePagination(): void
    {
        $pagination = new Pagination(134, 3, 10, '/orders', 'page_num');

        $this->assertSame(3, $pagination->getCurrentPage());
        $this->assertSame(14, $pagination->getLastPage());
        $this->assertSame(20, $pagination->getOffset());
        $this->assertSame(10, $pagination->getPageLimit());
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, null, 14], $pagination->getPages());
    }
}
