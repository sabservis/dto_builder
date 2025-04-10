<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder;

use ReflectionException;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorClass;
use SabServis\DTOBuilder\Exception\DTOCreationException;

class DTOArrayBuilder extends DTOBuilder
{
    /** @var array<DTOBuilderConstructorClass<AbstractDTO>> */
    private array $constructorClassCache = [];

    /**
     * @template T of \SabServis\DTOBuilder\DTO\AbstractDTO
     * @param class-string<T> $className
     * @return T
     * @throws DTOCreationException
     * @throws ReflectionException
     * @throws \SabServis\DTOBuilder\Exception\DTOValidationException
     */
    public function build(
        string $className,
        mixed $data,
    ): AbstractDTO {
        if (!isset($this->constructorClassCache[$className])) {
            $this->constructorClassCache[$className] = new DTOBuilderConstructorClass($className);
        }

        $constructorClass = $this->constructorClassCache[$className];

        // Příprava argumentů pro konstruktor na základě požadovaných parametrů
        $args = [];

        foreach ($constructorClass->getParameters() as $parameter) {
            [$value, $valueProvided] = $this->getValue($parameter->getName(), $data);

            $args[] = $this->hydrateValue($value, $parameter, $data, $valueProvided);
        }

        if (!$args) {
            return (new $className())
                ->setValidator($this->validator)
                ->setAbstractDtoInputData($data);
        }

        $this->validateDTOConstruction($args, $constructorClass->getParameters());

        // Vytvoření nové instance DTO s dynamicky naplněnými parametry
        return (new $className(...$args))
            ->setValidator($this->validator)
            ->setAbstractDtoInputData($data);
    }

    /**
     * @return array{0: ?mixed, 1: bool}
     */
    public function getValue(
        string $paramName,
        mixed $data,
    ): array {
        $valueProvided = array_key_exists($paramName, $data);

        if ($valueProvided) {
            return [$data[$paramName], true];
        }

        if (str_contains($paramName, '.')) {
            $delimiterPosition = (int)strpos($paramName, '.');
            $firstLevelPropertyName = substr($paramName, 0, $delimiterPosition);
            $innerArray = $data[$firstLevelPropertyName];

            if (is_array($innerArray)) {
                return $this->getValue(substr($paramName, $delimiterPosition + 1), $innerArray);
            }

            if (is_object($innerArray)) {
                $entityBuilder = $this->serviceLocator->get(DTOEntityBuilder::class);
                \assert($entityBuilder instanceof DTOEntityBuilder);
                $innerEntityReflection = $entityBuilder->getSourceClass($innerArray);

                return $entityBuilder->getValue(
                    $innerEntityReflection,
                    substr($paramName, $delimiterPosition + 1),
                    $innerArray,
                );
            }
        }

        // Zde můžete zpracovat případ, kdy array nemá požadovaný klíč
        // Například nastavit na null, použít výchozí hodnotu, nebo vyvolat výjimku
        return [null, false];
    }
}
