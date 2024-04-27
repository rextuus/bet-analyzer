<?php
declare(strict_types=1);

namespace App\Twig\Data;


class KeyValueListingContainer
{
    /**
     * @var array<string>
     */
    private array $keys = [];

    /**
     * @var array<string>
     */
    private array $values = [];

    /**
     * @var array<string>
     */
    private array $keyClasses = [];

    /**
     * @var array<string>
     */
    private array $valueClasses = [];

    /**
     * @var array<string>
     */
    private array $containerClasses = [];

    /**
     * @param array<string, string> $content
     */
    public function createByArray(array $content): void
    {
        $this->keys = array_keys($content);
        $this->values = array_values($content);
    }

    /**
     * @param array<string> $keyClasses
     * @param array<string> $valueClasses
     */
    public function addEntry(string $key, string $value, array $keyClasses = [], array $valueClasses = []): KeyValueListingContainer
    {
        $this->keys[] = $key;
        $this->values[] = $value;
        $this->keyClasses[] = $keyClasses;
        $this->valueClasses[] = $valueClasses;

        return $this;
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    public function setKeys(array $keys): KeyValueListingContainer
    {
        $this->keys = $keys;
        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): KeyValueListingContainer
    {
        $this->values = $values;
        return $this;
    }

    public function getKeyClasses(): array
    {
        return $this->keyClasses;
    }

    public function setKeyClasses(array $keyClasses): KeyValueListingContainer
    {
        $this->keyClasses = $keyClasses;
        return $this;
    }

    public function getValueClasses(): array
    {
        return $this->valueClasses;
    }

    public function setValueClasses(array $valueClasses): KeyValueListingContainer
    {
        $this->valueClasses = $valueClasses;
        return $this;
    }

    public function getContainerClasses(): array
    {
        return $this->containerClasses;
    }

    public function setContainerClasses(array $containerClasses): KeyValueListingContainer
    {
        $this->containerClasses = $containerClasses;
        return $this;
    }
}
