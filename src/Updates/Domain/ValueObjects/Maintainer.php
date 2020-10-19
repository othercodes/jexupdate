<?php

namespace JEXUpdate\Updates\Domain\ValueObjects;

use OtherCode\DDDValueObject\IsValueObject;

/**
 * Class Maintainer
 *
 * @property string name
 * @property string url
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class Maintainer
{
    use IsValueObject;

    /**
     * Maintainer constructor.
     *
     * @param string $name
     * @param string $url
     */
    public function __construct(string $name, string $url)
    {
        $this->hydrate(
            [
                'name' => $name,
                'url'  => $url,
            ]
        );
    }

    protected function invariantNameMustNotBeEmpty(): bool
    {
        return !empty($tmp = $this->name);
    }

    protected function invariantUrlMustHasValidFormat(): bool
    {
        return filter_var($this->url, FILTER_VALIDATE_URL);
    }

    public function name(): string
    {
        return $this->get('name');
    }

    public function url(): string
    {
        return $this->get('url');
    }
}
