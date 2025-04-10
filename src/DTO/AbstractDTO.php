<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO;

use SabServis\DTOBuilder\Exception\DTOValidationException;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractDTO implements \JsonSerializable
{
    //phpcs:ignore
    private ?ValidatorInterface $_validator;

    private mixed $abstractDtoInputData;

    public function setValidator(?ValidatorInterface $validator): static
    {
        $this->_validator = $validator;

        return $this;
    }

    public function setAbstractDtoInputData(mixed $inputParams): static
    {
        $this->abstractDtoInputData = $inputParams;

        return $this;
    }

    public function existInInputData(string $name): bool
    {
        if (is_array($this->abstractDtoInputData) === false) {
            return false;
        }

        return array_key_exists($name, $this->abstractDtoInputData);
    }

    public function isNullInInputData(string $name): bool
    {
        return $this->existInInputData($name) && $this->abstractDtoInputData[$name] === null;
    }

    /**
     * @param string|GroupSequence|array<string|GroupSequence>|null $validationGroups The validation groups to validate. If none is given, "Default" is assumed
     * @throws \SabServis\DTOBuilder\Exception\DTOValidationException
     */
    public function validate(string|GroupSequence|array|null $validationGroups = null): void
    {
        if (!$this->_validator) {
            throw new \Exception('Validator not set');
        }

        $errors = $this->_validator->validate($this, null, $validationGroups);

        if ($errors->count() > 0) {
            throw new DTOValidationException($errors);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = get_object_vars($this);
        unset($array['_validator']);
        unset($array['abstractDtoInputData']);
        $ret = [];

        foreach ($array as $key => $item) {
            if ($item instanceof \UnitEnum) {
                $ret[$key] = $item->name;

                continue;
            }

            if ($item instanceof \DateTimeInterface) {
                $ret[$key] = $item->format('Y-m-d\TH:i:sP');

                continue;
            }

            if ($item instanceof self) {
                $ret[$key] = $item->toArray();

                continue;
            }

            if (is_array($item)) {
                $arr = $this->iterateArrayToArray($item);
                $ret[$key] = $arr;

                continue;
            }

            $ret[$key] = $item;
        }

        return $ret;
    }

    /**
     * Returns array for patch save
     * It means - not exist value in $inputParams -> it will NOT be in returned array
     *
     * @return array<string, mixed>
     */
    public function toArrayForPatch(): array
    {
        return $this->patchArray($this->abstractDtoInputData, $this->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param array<mixed> $item
     * @return array<mixed>
     */
    private function iterateArrayToArray(array $item): array
    {
        $arr = [];

        foreach ($item as $subKey => $subitem) {
            if (is_array($subitem)) {
                $arr[$subKey] = $this->iterateArrayToArray($subitem);

                continue;
            }

            $arr[$subKey] = $subitem instanceof self ? $subitem->toArray() : $subitem;
        }

        return $arr;
    }

    /**
     * @param array<mixed> $item
     * @param array<mixed> $values
     * @return array<string, mixed>
     */
    private function patchArray(
        array $item,
        array $values,
    ): array {
        $result = $item;

        // Procházení pole a aplikace hodnot, pokud jsou dostupné
        foreach ($item as $key => $value) {
            if (is_array($value)) {
                // Rekurzivní zpracování pro zanořená pole
                $result[$key] = $this->patchArray($value, $values);
            } elseif (array_key_exists($key, $values)) {
                // Nastavení hodnoty, pokud existuje klíč ve výstupním poli
                $result[$key] = $values[$key];
            }
        }

        return $result;
    }
}
