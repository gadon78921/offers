<?php

declare(strict_types=1);

namespace App\Esb\In\AssortmentUnitSpecification\Messages;

final class VarNode
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $type,
        public readonly ?string $value,
    ) {}

    public function value(): mixed
    {
        return $this->denormalize($this->value, $this->type);
    }

    private function denormalize(int|string|bool|null $value, string $type): mixed
    {
        if (null === $value) {
            return null;
        }

        if (str_ends_with($type, '[]')) {
            $type  = substr($type, 0, -2);
            $value = array_values(array_map(
                function ($value) use ($type) {
                    return $this->denormalize($value, $type);
                },
                (array) (json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR) ?? [])
            ));
        } elseif ('bool' === $type) {
            $value = 'true' === $value;
        } else {
            settype($value, $type);
        }

        return $value;
    }
}
