<?php

namespace Luclin2\Foundation;

use Luclin2\Utilities\{
    Pack,
    Recursive,
};

/**
 * 序列化与还原
 */

/**
 * 提供一个确定的结构以及相关的访问及校验功能
 */
/**
 * Meta 类
 *
 * INFO: 值的设计原则之一，null值不再作为一种数据值使用，只能作为他的本意，却表示未设值。
 *
 * INFO: 根据null仅作为未设值表意，对六种勾子方法的调用规则做出定义。
 * 仅对非null值进行调用的：
 * - `_array_`
 * - `_set_`
 * - `_get_` // 展示的问题交由展示层处理
 *
 * 会对null值进行调用的：
 * - `_confirm_`
 * - `_isset_`
 * - `_unset_`
 */
abstract class Meta implements \ArrayAccess, \JsonSerializable, \IteratorAggregate
{
    use MacroableTrait;

    protected array $properties = [];

    protected static array $_deprecated = [];

    protected static array $_mappings = [];

    protected static bool $_withDeprecated = false;

    protected ?array $_origins = null;

    abstract protected static function _defaults(): array;

    protected static function _virtuals(): array {
        return [];
    }

    public function __construct(?array $properties)
    {
        $properties && $this->fill($properties);
        // 在构造器中fill()不会产生为null的原数据
        $this->_origins = [];
    }

    /**
     * 获取结构配置。
     * 此结构中不包括废弃字段
     *
     * @return array
     */
    public static function defaults(): array
    {
        static $availables = [];
        if (static::$_withDeprecated) {
            return static::_defaults();
        }

        if (!$availables) {
            foreach (static::_defaults() as $key => $default) {
                !static::isDeprecatedKey($key) && $availables[$key] = $default;
            }
        }
        return $availables;
    }

    /**
     * 是否为废弃字段
     *
     * @param  string|int $key
     * @return bool
     */
    public static function isDeprecatedKey(string|int $key): bool
    {
        return isset(static::$_deprecated[$key]);
    }

    /**
     * 传入一个闭包，在闭包内将可使用迭代器访问废弃字段
     *
     * @param  callable $handle
     * @return mixed
     */
    public function withDeprecated(callable $handle): mixed {
        static::$_withDeprecated = true;
        $result = $handle($this);
        static::$_withDeprecated = false;
        return $result;
    }

    public function fill(?iterable $properties, array $excludes = []): static {
        if (!$properties) {
            return $this;
        }

        $excludeKeys = [];
        foreach ($excludes as $key) {
            $excludeKeys[$key] = 1;
        }

        foreach ($properties as $key => $value) {
            if (isset($excludeKeys[$key])) {
                continue;
            }
            // INFO: mapping 仅在fill时生效
            $key = static::$_mappings[$key] ?? $key;
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * 迭代递归将所有层级转换为数组
     *
     * @param  callable $filter
     * @param  bool $nullable
     * @return array
     */
    public function toArray(callable $filter = null, $nullable = true): array
    {
        $toArray = new Recursive\ToArray(
            root: $this->iterate(),
            nullable: $nullable,
            filter: $filter,
        );
        $result = $toArray();
        foreach ($result as $key => $value) {
            $method = "_array_$key";
            if ($value !== null && method_exists($this, $method)) {
                $value = $this->$method($value);
                // 赋回
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function __unserialize(array $data): void
    {
        $this->fill($data);
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function jsonSerialize(): array
    {
        // INFO: json序列化将不保留null值
        return $this->toArray(null, false);
    }

    public function toBin(): string
    {
        // INFO: 二进制序列化将不保留null值
        return Pack::encode($this->toArray(null, false));
    }

    public static function fromBin(string $bin): static
    {
        return new static(Pack::decode($bin));
    }

    protected function callVirtualProperty(string $key): mixed {
        $virtuals = static::_virtuals();
        if (!isset($virtuals[$key])) {
            return null;
        }
        return $virtuals[$key]->call($this);
    }

    protected function backupProperty(string|int $key): void {
        is_array($this->_origins) && !\array_key_exists($key, $this->_origins) &&
            ($this->_origins[$key] = $this->properties[$key] ?? null);
    }

    public function getPatch(): array {
        $patch = [];
        foreach (array_keys($this->_origins) as $key) {
            $patch[$key] = $this->$key;
        }
        return $patch;
    }

    public function getOrigin(): static {
        return new static(array_merge($this->properties, $this->_origins));
    }

    public function confirm(): static
    {
        // confirm勾子
        foreach ($this->iterate() as $key => $value) {
            $method = "_confirm_$key";
            if (method_exists($this, $method)) {
                $value = $this->$method($value);
                // 赋回
                $this->$key = $value;
            }
        }

        $this->_origins = [];

        return $this;
    }

    public function __set(string|int $key, mixed $value): void
    {
        $method = "_set_$key";
            // INFO: set 勾子仅在值不为null时被调用
        if ($value !== null && method_exists($this, $method)) {
            $value = $this->$method($value);
        }

        $this->backupProperty($key);
        $this->properties[$key] = $value;
    }

    /**
     * 抽取属性逻辑
     */
    public function __get(string|int $key): mixed
    {
        $value  = $this->properties[$key] ?? null;
        // null的话
        if ($value === null) {
            $value = static::defaults()[$key] ?? $this->callVirtualProperty($key);
        }

        $method = "_get_$key";
        if ($value !== null && method_exists($this, $method)) {
            $value = $this->$method($value);
        }
        return $value;
    }

    public function __isset(string|int $key): bool {
        $method = "_isset_$key";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return array_key_exists($key, $this->properties);
    }

    public function __unset(string|int $key): void {
        $method = "_unset_$key";
        // INFO: 自定义 unset 勾子中默认返回void，交由框架unset。如要自行unset需要返回true。如果存在中断机制，在返回true的同时，要自行处理是否需要调`$this->backupProperty($key)`
        if (method_exists($this, $method) && $this->$method()) {
            return;
        }

        $this->backupProperty($key);
        unset($this->properties[$key]);
    }

    /**
     * 用于复原变量export数据的方法。
     *
     * @param  array $states
     * @return static
     */
    public static function __set_state(array $states): static
    {
        $meta = new static($states['properties']);
        isset($states['_origins']) && ($meta->_origins = $states['_origins']);
        return $meta;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->$offset);
    }

    /**
     * 返回一个遍历内部数据的迭代器
     *
     * @return iterable
     */
    public function iterate(): iterable {
        foreach (static::defaults() as $key => $_default) {
            yield $key => $this->$key;
        }
    }

    public function getIterator(): \Traversable
    {
        return $this->iterate();
    }
}