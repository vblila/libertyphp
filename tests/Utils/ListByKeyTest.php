<?php declare(strict_types=1);

namespace Libertyphp\Tests\Utils;

use Libertyphp\Utils\ListByKey;
use PHPUnit\Framework\TestCase;

final class ListByKeyTest extends TestCase
{
    public function testArrayByColumn(): void
    {
        $users = [
            ['num' => 0, 'id' => 101, 'name' => 'Ivanov', 'age' => 18],
            ['num' => 1, 'id' => 102, 'name' => 'Petrov', 'age' => 22],
            ['num' => 2, 'id' => 103, 'name' => 'Sidorov', 'age' => 10],
        ];

        $usersById = ListByKey::get('id', $users);

        $this->assertSame(
            [
                101 => ['num' => 0, 'id' => 101, 'name' => 'Ivanov', 'age' => 18],
                102 => ['num' => 1, 'id' => 102, 'name' => 'Petrov', 'age' => 22],
                103 => ['num' => 2, 'id' => 103, 'name' => 'Sidorov', 'age' => 10],
            ],
            $usersById
        );
    }

    public function testObjectsByProperty(): void
    {
        $users = [
            new TestUser(101, 'Ivanov'),
            new TestUser(102, 'Petrov'),
            new TestUser(103, 'Sidorov'),
        ];

        /** @var TestUser[] $usersById */
        $usersById = ListByKey::get('id', $users);

        $this->assertCount(3, $usersById);

        $this->assertSame('Ivanov', $usersById[101]->name);
        $this->assertSame('Petrov', $usersById[102]->name);
        $this->assertSame('Sidorov', $usersById[103]->name);
    }
}
