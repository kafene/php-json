<?php

namespace Kafene\Json;

use InvalidArgumentException;

abstract class JsonAbstract
{
    use HasFlagsTrait;

    /* PHP default max recursion depth */
    const DEFAULT_DEPTH = 512;

    protected int $depth = 512;

    /** @var mixed */
    protected $input;

    /** @param mixed $input */
    final public function __construct($input = null)
    {
        $this->applyDefaultFlags();

        $this->input = $input;
    }

    public function withInput($input)
    {
        $object = clone $this;
        $object->input = $input;

        return $object;
    }

    public function getInput()
    {
        return $this->input;
    }

    abstract protected function applyDefaultFlags(): void;

    public function depth(int $depth): static
    {
        if ($depth < 1) {
            throw new InvalidArgumentException('JSON recursion depth must be greater than 0');
        }

        $this->depth = $depth;

        return $this;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function substituteInvalidUtf8(bool $apply = true): static
    {
        if ($apply && $this->hasFlag(JSON_INVALID_UTF8_IGNORE)) {
            $this->removeFlag(JSON_INVALID_UTF8_IGNORE);
        }

        return $this->applyFlag(JSON_INVALID_UTF8_SUBSTITUTE, $apply);
    }

    public function isSubstitutingInvalidUtf8(): bool
    {
        return $this->hasFlag(JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function ignoreInvalidUtf8(bool $apply = true): static
    {
        if ($apply && $this->hasFlag(JSON_INVALID_UTF8_SUBSTITUTE)) {
            $this->removeFlag(JSON_INVALID_UTF8_SUBSTITUTE);
        }

        return $this->applyFlag(JSON_INVALID_UTF8_IGNORE, $apply);
    }

    public function isIgnoringInvalidUtf8(): bool
    {
        return $this->hasFlag(JSON_INVALID_UTF8_IGNORE);
    }

    public function throwOnError(bool $apply = true): static
    {
        return $this->applyFlag(JSON_THROW_ON_ERROR, $apply);
    }

    public function isThrowingOnError(): bool
    {
        return $this->hasFlag(JSON_THROW_ON_ERROR);
    }
}
