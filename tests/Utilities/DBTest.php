<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Luclin2\Utilities\DB;

final class DBTest extends TestCase
{
    public function testToProperty(): void
    {
        $this->assertEquals('createdBy', DB::toProperty('created_by'));
    }

    public function testToField(): void
    {
        $this->assertEquals('last_login_time', DB::toField('lastLoginTime'));
    }
}
