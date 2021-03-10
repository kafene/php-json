<?php

namespace Kafene\Json;

trait HasFlagsTrait
{
    protected int $flags = 0;

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function hasFlag(int $flag): bool
    {
        return ($this->flags & $flag) !== 0;
    }

    protected function addFlag(int $flag): void
    {
        $this->flags |= $flag;
    }

    protected function removeFlag(int $flag): void
    {
        $this->flags &= ~$flag;
    }

    protected function applyFlag(int $flag, bool $apply): static
    {
        if ($apply) {
            $this->addFlag($flag);
        } else {
            $this->removeFlag($flag);
        }

        return $this;
    }
}
