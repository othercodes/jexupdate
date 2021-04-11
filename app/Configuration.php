<?php

declare(strict_types=1);

namespace JEXServer;

/**
 * Class Configuration
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package JEXServer
 */
class Configuration
{
    public string $name;
    public string $description;
    public array $extensions;

    /**
     * Configuration constructor.
     *
     * @param  array  $configuration
     */
    public function __construct(array $configuration)
    {
        $this->name = $configuration['name'];
        $this->description = $configuration['description'];
        $this->extensions = $configuration['extensions'];
    }
}