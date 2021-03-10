<?php

namespace Kafene\Json;

use InvalidArgumentException;

class Decoder extends JsonAbstract
{
    protected bool $assoc = false;

    protected function applyDefaultFlags(): void
    {
        $this->throwOnError();
        $this->substituteInvalidUtf8();
    }

    /** @return mixed */
    public function get()
    {
        return json_decode($this->input, $this->assoc, $this->getDepth(), $this->getFlags());
    }

    /** @return mixed */
    public function getAssoc()
    {
        return $this->assoc(true)->get();
    }

    public function assoc(bool $apply = true): static
    {
        $this->assoc = $apply;
        $this->applyFlag(JSON_OBJECT_AS_ARRAY, $apply);

        return $this;
    }

    public function isAssoc(): bool
    {
        return $this->assoc || $this->hasFlag(JSON_OBJECT_AS_ARRAY);
    }

    public function objectAsArray(bool $apply = true): static
    {
        return $this->assoc($apply);
    }

    public function bigintAsString(bool $apply = true): static
    {
        return $this->applyFlag(JSON_BIGINT_AS_STRING, $apply);
    }
}
