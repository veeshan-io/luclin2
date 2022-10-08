<?php

declare(strict_types=1);

require_once(__DIR__.'/Meta/UserMetaMock.php');

use PHPUnit\Framework\TestCase;

final class MetaTest extends TestCase
{
    public UserMetaMock $meta;

    protected function setUp(): void
    {
        $this->meta = new UserMetaMock([
            'id'        => 900001,
            'name'      => 'Neprius',
            'gender'    => 1,
            'mobile'    => '101212337',
            'bio'       => 'It\'s me!',
            'nonono'    => 'deprecated',
        ]);
    }

    protected function tearDown(): void
    {
    }

    /**
     * 创建
     *
     * @return void
     */
    public function testCreate(): void
    {
        $properties = \luc\fetch($this->meta, 'properties');
        $this->assertEquals('Neprius', $properties['name']);
        $this->assertEquals(1, $properties['gender']);
    }

    /**
     * 获取默认值
     *
     * @return void
     */
    public function testDefaults(): void
    {
        $defaults = UserMetaMock::defaults();
        $this->assertEquals(UserMetaMock::TYPE_NORMAL, $defaults['type']);
    }

    /**
     * 转换到数组
     *
     * @return void
     */
    public function testToArray(): void
    {
        $properties = \luc\fetch($this->meta, 'properties');
        $array = $this->meta->toArray();
        $this->assertEquals($properties['name'], $array['name']);
    }

    /**
     * 废弃字段判断
     *
     * @return void
     */
    public function testIsDeprecatedKey(): void
    {
        $this->assertTrue($this->meta->isDeprecatedKey('nonono'));
        $this->assertFalse($this->meta->isDeprecatedKey('name'));
        $this->assertArrayNotHasKey('nonono' ,$this->meta->toArray());
    }

    /**
     * 操作废弃字段
     *
     * @return void
     */
    public function testWithDeprecated(): void
    {
        $data = $this->meta->withDeprecated(function($meta) {
            return $meta->toArray();
        });
        $this->assertArrayHasKey('nonono', $data);
        $this->assertEquals('deprecated', $data['nonono']);
    }

    /**
     * 填充
     *
     * @return void
     */
    public function testFill(): void
    {
        $this->meta->fill([
            'name'  => 'Nebo',
            'email' => 'nebo@163.com',
            'bio'   => 'skipped',
        ], [
            'bio',
        ]);
        $properties = \luc\fetch($this->meta, 'properties');
        $this->assertEquals('Nebo', $properties['name']);
        $this->assertEquals('nebo@163.com', $properties['email']);
        $this->assertEquals('101212337', $properties['mobile']);
        $this->assertEquals('It\'s me!', $properties['bio']);
    }

    /**
     * 测试映射功能
     *
     * @return void
     */
    public function testMappings(): void
    {
        $this->meta->latestLoginTime = \luc\time::now()->format('Y-m-d H:i:s');
        $this->assertEquals(\luc\time::now()->format('Y-m-d H:i:s'), $this->meta->toArray()['latestLoginTime']);
    }

    /**
     * 属性访问，即__set()/__get()/__isset()/__unset()等方法
     *
     * @return void
     */
    public function testPropertiesAccess(): void
    {
        // 原属性测试
        $this->assertNull($this->meta->mark);
        $this->assertFalse(isset($this->meta->mark));
        $this->meta->mark = 'test mark';
        $this->assertEquals('test mark', $this->meta->mark);
        $this->meta->mark = '';
        $this->assertEquals('', $this->meta->mark);
        $this->assertTrue(isset($this->meta->mark));
        unset($this->meta->mark);
        $this->assertFalse(isset($this->meta->mark));

        // 勾子方法测试
        $this->meta->markSet = 'test mark';
        $this->meta->markGet = 'test mark';
        $this->assertEquals('Set test mark', $this->meta->markSet);
        $this->assertEquals('Get test mark', $this->meta->markGet);

        // TODO: isset和unset勾子暂时未测试
    }

    /**
     * 序列化与反序列化测试
     *
     * @return void
     */
    public function testSerialize(): void
    {
        // \luc\du(serialize($this->meta));
        $str = serialize($this->meta);
        $this->assertEquals(384, strlen($str));
        $meta = unserialize($str);
        $str2 = serialize($meta);
        $this->assertEquals($str, $str2);
    }

    /**
     * 转换到字串
     *
     * @return void
     */
    public function testToString(): void
    {
        $str = "$this->meta";
        $this->assertEquals(106, strlen($str));
    }

    /**
     * Json序列化与反序列化
     *
     * @return void
     */
    public function testJson(): void
    {
        $json = json_encode($this->meta);
        $this->assertEquals("$this->meta", $json);
    }

    /**
     * 二进制转换与恢复
     *
     * @return void
     */
    public function testBin(): void
    {
        $bin = $this->meta->toBin();
        $meta = UserMetaMock::fromBin($bin);
        $this->assertEquals(serialize($this->meta), serialize($meta));
    }

    /**
     * 虚拟属性
     *
     * @return void
     */
    public function testVirtualProperty(): void
    {
        $this->assertEquals('Neprius#900001', $this->meta->tagId);
    }

    /**
     * 补丁功能两接口测试
     *
     * @return void
     */
    public function testPatch(): void
    {
        $originMenual = "$this->meta";
        $this->meta->name = 'Nebo';
        $this->meta->fill([
            'id'        => 800001,
            'gender'    => 0,
        ]);
        $this->assertEquals('Nebo', $this->meta->name);
        $this->assertEquals(800001, $this->meta->id);
        $this->assertEquals(0, $this->meta->gender);
        $this->assertEquals($originMenual, "{$this->meta->getOrigin()}");
        $this->assertEquals($originMenual, "{$this->meta->getOrigin()}");
        $patch = $this->meta->getPatch();
        $this->assertEquals(3, count($patch));
        $this->assertEquals('Nebo', $patch['name']);
        $this->assertEquals(800001, $patch['id']);
        $this->assertEquals(0, $patch['gender']);
    }

    /**
     * confirm机制
     *
     * @return void
     */
    public function testConfirm(): void
    {
        $this->meta->mark = 'test mark';
        $this->meta->confirm();
        $this->assertEquals('Confirm test mark', $this->meta->mark);
    }

    /**
     * SetState机制
     *
     * @return void
     */
    public function testSetState(): void
    {
        $exported = var_export($this->meta, true);
        $restored = new \stdClass();
        eval('$restored = '.$exported.';');
        $this->assertEquals($this->meta->name, $restored->name);
        $this->assertEquals($this->meta->id, $restored->id);
        $this->assertEquals($this->meta->gender, $restored->gender);
        $this->assertEquals($this->meta->mobile, $restored->mobile);
        $this->assertEquals($this->meta->bio, $restored->bio);
    }

    /**
     * 数组形式访问
     *
     * @return void
     */
    public function testArrayAccess(): void
    {
        $this->assertEquals('Neprius', $this->meta['name']);
        $this->assertEquals(900001, $this->meta['id']);
        unset($this->meta['mobile']);
        $this->assertArrayNotHasKey('mobile', $this->meta);
        $this->meta['mobile'] = '123321';
        $this->assertEquals('123321', $this->meta['mobile']);
        $this->assertTrue(isset($this->meta['name']));
    }

    /**
     * 遍历机制
     *
     * @return void
     */
    public function testIterate(): void
    {
        $array = $this->meta->toArray();
        foreach ($this->meta as $key => $value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertEquals($array[$key], $value);
            unset($array[$key]);
        }
        $this->assertEquals([], $array);
    }
}
