<?php

namespace JEXUpdate\Updates\Domain\ValueObjects;

use Exception;
use InvalidArgumentException;
use OtherCode\DDDValueObject\IsValueObject;

/**
 * Class DownloadURL
 *
 * @property string url
 * @property string format
 * @property string type
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class DownloadURL
{
    use IsValueObject;

    /**
     * Allowed list of package formats.
     *
     * @var array|string[]
     */
    private array $allowedFormats = ['zip', 'tar'];

    /**
     * Allowed list of package types.
     *
     * @var array|string[]
     */
    private array $allowedTypes = ['full', 'upgrade'];

    /**
     * DownloadURL constructor.
     *
     * @param  string  $url
     * @param  string  $format
     * @param  string  $type
     *
     * @throws Exception
     */
    public function __construct(string $url, string $format, string $type)
    {
        $this->hydrate(
            [
                'url'    => $url,
                'format' => $format,
                'type'   => $type,
            ]
        );
    }

    protected function invariantUrlMustHasValidFormat(): bool
    {
        return filter_var($this->url, FILTER_VALIDATE_URL);
    }

    protected function invariantFormatMustBeOneOfAllowed(): bool
    {
        if (!in_array($this->format, $this->allowedFormats)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid format, must be one of: %s",
                    implode(', ', $this->allowedFormats)
                )
            );
        }

        return true;
    }

    protected function invariantTypeMustBeOneOfAllowed(): bool
    {
        if (!in_array($this->type, $this->allowedTypes)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid Type, must be one of: %s",
                    implode(', ', $this->allowedTypes)
                )
            );
        }

        return true;
    }

    public function url(): string
    {
        return $this->get('url');
    }

    public function format(): string
    {
        return $this->get('format');
    }

    public function type(): string
    {
        return $this->get('type');
    }
}
