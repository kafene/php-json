<?php

namespace Kafene\Json;

class Encoder extends JsonAbstract
{
    protected function applyDefaultFlags(): void
    {
        $this->throwOnError();
        $this->substituteInvalidUtf8();
        $this->preserveZeroFraction();
    }

    /** @return string|false */
    public function get()
    {
        return json_encode($this->input, $this->getFlags(), $this->getDepth());
    }

    /** @return string */
    public function __toString()
    {
        return (string) $this->get();
    }

    public function forceObject(bool $apply = true): static
    {
        return $this->applyFlag(JSON_FORCE_OBJECT, $apply);
    }

    public function preserveZeroFraction(bool $apply = true): static
    {
        return $this->applyFlag(JSON_PRESERVE_ZERO_FRACTION, $apply);
    }

    public function partialOutputOnError(bool $apply = true): static
    {
        return $this->applyFlag(JSON_PARTIAL_OUTPUT_ON_ERROR, $apply);
    }

    public function unescapedUnicode(bool $apply = true): static
    {
        return $this->applyFlag(JSON_UNESCAPED_UNICODE, $apply);
    }

    public function unescapedSlashes(bool $apply = true): static
    {
        return $this->applyFlag(JSON_UNESCAPED_SLASHES, $apply);
    }

    public function unescapedLineTerminators(bool $apply = true): static
    {
        return $this->applyFlag(JSON_UNESCAPED_LINE_TERMINATORS, $apply);
    }

    public function unescaped(bool $apply = true): static
    {
        return $this->unescapedUnicode($apply)
                    ->unescapedSlashes($apply)
                    ->unescapedLineTerminators($apply);
    }

    public function prettyPrint(bool $apply = true): static
    {
        return $this->applyFlag(JSON_PRETTY_PRINT, $apply);
    }

    public function numericCheck(bool $apply = true): static
    {
        return $this->applyFlag(JSON_NUMERIC_CHECK, $apply);
    }

    public function hexAmp(bool $apply = true): static
    {
        return $this->applyFlag(JSON_HEX_AMP, $apply);
    }

    public function hexTag(bool $apply = true): static
    {
        return $this->applyFlag(JSON_HEX_TAG, $apply);
    }

    public function hexApos(bool $apply = true): static
    {
        return $this->applyFlag(JSON_HEX_APOS, $apply);
    }

    public function hexQuot(bool $apply = true): static
    {
        return $this->applyFlag(JSON_HEX_QUOT, $apply);
    }

    public function hex(bool $apply = true): static
    {
        return $this->hexAmp($apply)
                    ->hexTag($apply)
                    ->hexApos($aply)
                    ->hexQuot($apply);
    }

    public function readable(bool $apply = true): static
    {
        return $this->prettyPrint($apply)
                    ->unescapedUnicode($apply)
                    ->unescapedSlashes($apply)
                    ->unescapedLineTerminators($apply);
    }
}
