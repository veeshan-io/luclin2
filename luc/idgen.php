<?php

namespace luc;

// TODO: 定义version有效期到2149年 可通过version延长 version为62进制
function gid(int $now = 0, string $shard = '000',
    int $randomBytes = 4, $version = '1'): string
{
    // 获取微秒时间戳
    !$now && $now = microtime(true);
    // 减去偏移量并精确到100微秒
    $now = ($now - 1590000000) * 10000;
    // 生成16进制时间戳
    $stamp = str_pad(dechex($now), 12, '0', \STR_PAD_LEFT);
    // 生成16进制随机数
    $random = bin2hex(openssl_random_pseudo_bytes($randomBytes));
    // 拼上16进制分片组成16进制字串
    $hex = "$stamp$shard$random";
    // 计算出62进制值
    $id = gmp_strval(gmp_init("0x$hex", 16), 62);
    // 确保 16 位
    if ($version) {
        return $version.substr(str_pad($id, 15, '0', \STR_PAD_LEFT), 0, 15);
    }

    // 不定义version则有效期超过4049年 但是当需要对数据做分割时会遇到麻烦
    return substr(str_pad($id, 16, '0', \STR_PAD_LEFT), 0, 16);
}

// TODO: sid不能做主键id，并且使用的时候结合其他字段条件进行筛选，仅作为次级数据排序之用
function sid(string $gid, int $base, int $now = 0): string
{
    // 取gid作为前缀
    $prefix = substr($gid, 0, 8);

    // 获取秒时间戳
    !$now && $now = time();

    // 计算出时间偏移量实现排序
    // TODO: 偏移量限制在776.7天，超过后排序将轮回
    $time = abs($now - $base) % (67108863 - 1);

    // 生成16进制时间戳
    $stamp = str_pad(dechex($time), 7, '0', \STR_PAD_LEFT);
    // 生成16进制随机数
    $random = substr(bin2hex(openssl_random_pseudo_bytes(3)), 0, 5);
    // 组成16进制字串
    $hex = "$stamp$random";
    // 计算出62进制值
    $id = gmp_strval(gmp_init("0x$hex", 16), 62);
    // 确保 16 位
    return $prefix.substr(str_pad($id, 8, '0', \STR_PAD_LEFT), 0, 8);
}
