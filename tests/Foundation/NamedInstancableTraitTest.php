<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Luclin2\Utilities\Dock;

final class NamedInstancableTraitMock
{
    use Luclin2\Foundation\NamedInstancableTrait;

    public function __construct(
        private string $key,
        private $value = null,
    ) {
    }

    public function __toString(): string
    {
        return "$this->key:$this->value";
    }
}

final class NamedInstancableTraitTest extends TestCase
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function testInstanceSame(): void
    {
        $first = NamedInstancableTraitMock::instance(arguments: [
            'key'   => 'test mock',
        ]);
        $same = NamedInstancableTraitMock::instance();
        $this->assertEquals("$same", 'test mock:');

        $first = NamedInstancableTraitMock::instance(arguments: [
            'key'   => 'test mock2',
        ]);
        // no change
        $this->assertEquals("$first", 'test mock:');
    }

    public function testInstanceNamed(): void
    {
        $diana = NamedInstancableTraitMock::instance(arguments: [
            'key'   => 'diana mock',
            'value' => 'diana value',
        ], name: 'diana');
        $dianaSame = NamedInstancableTraitMock::instance('diana');
        $this->assertEquals("$dianaSame", 'diana mock:diana value');
    }
}
