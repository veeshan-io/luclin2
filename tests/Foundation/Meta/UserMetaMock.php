<?php

require_once(__DIR__ . '/BaseMetaMock.php');

final class UserMetaMock extends BaseMetaMock
{
    const TYPE_NORMAL = 1;

    protected static array $_deprecated = [
        'nonono'    => 1,
    ];

    protected static array $_mappings = [
        'latest_login_time'    => 'latestLoginTime',
        'frome_channels'    => 'from_channels',
        'banned_at'    => 'bannedAt',
    ];

    protected static function _defaults(): array
    {
        return [
            'type'      => self::TYPE_NORMAL,
            'name'      => null,
            'email'     => null,
            'mobile'    => null,
            'password'  => null,
            'gender'    => null,
            'avatar'    => null,
            'bio'       => null,
            'location'  => null,
            'latestLoginTime' => null,
            'fromChannels' => [],
            'bannedAt' => null,
            'nonono'    => 'deprecated',
            'mark'      => null,
            'markSet' => null,
            'markGet' => null,
            ] + parent::_defaults();
    }

    protected static function _virtuals(): array
    {
        return [
            'tagId' => function() {
                return "$this->name#$this->id";
            },
        ];
    }

    protected static function _set_latestLoginTime(string $value): \DateTimeImmutable
    {
        return \luc\time::create($value);
    }

    protected static function _array_latestLoginTime(\DateTimeImmutable $value): string
    {
        return $value->format('Y-m-d H:i:s');
    }

    protected static function _set_markSet(string $value): string
    {
        return "Set $value";
    }

    protected static function _get_markGet(string $value): string
    {
        return "Get $value";
    }

    protected static function _confirm_mark(?string $value): ?string
    {
        return $value ? "Confirm $value" : $value;
    }
}
