<?php

declare(strict_types=1);

namespace SabServis\DTOBuilder\DTO\Builder;

use ReflectionException;
use SabServis\DTOBuilder\DTO\AbstractDTO;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOBuilderConstructorClass;
use SabServis\DTOBuilder\DTO\Builder\PreloadedReflection\DTOEntityBuilderSourceClass;
use SabServis\DTOBuilder\Exception\DTOCreationException;

class DTOEntityBuilder extends DTOBuilder
{
    /** @var array<DTOBuilderConstructorClass<AbstractDTO>> */
    private array $constructorClassCache = [];

    /** @var array<DTOEntityBuilderSourceClass<object>> */
    private array $sourceClassCache = [];

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
        mixed $entity,
    ): AbstractDTO {
        $constructorClass = $this->getConstructorClass($className);

        // Příprava argumentů pro konstruktor na základě požadovaných parametrů
        $args = [];

        $sourceClass = $this->getSourceClass($entity);

        foreach ($constructorClass->getParameters() as $parameter) {
            $paramName = $parameter->getName();

            [$value, $valueProvided] = $this->getValue($sourceClass, $paramName, $entity);

            $args[] = $this->hydrateValue($value, $parameter, $entity, $valueProvided);
        }

        if (!$args) {
            return (new $className())->setValidator($this->validator);
        }

        $this->validateDTOConstruction($args, $constructorClass->getParameters());

        // Vytvoření nové instance DTO s dynamicky naplněnými parametry
        return (new $className(...$args))->setValidator($this->validator);
    }

    /**
     * @template T of object
     * @param DTOEntityBuilderSourceClass<T> $sourceClass
     *
     * @return array{0: ?mixed, 1: bool} $value,$valueProvided pair
     */
    public function getValue(
        DTOEntityBuilderSourceClass $sourceClass,
        string $paramName,
        mixed $entity,
    ): array {
        $getter = $sourceClass->getGetter($paramName);

        if ($getter) {
            return [$getter->invoke($entity), true];
        }

        $property = $sourceClass->getProperty($paramName);

        if ($property) {
            return [$property->getValue($entity), true];
        }

        $innerObject = $this->getInnerEntity($sourceClass, $paramName, $entity);

        if ($innerObject && is_object($innerObject)) {
            $innerEntityReflection = new DTOEntityBuilderSourceClass($innerObject::class);
            $delimiterPosition = (int)strpos($paramName, '.');

            return $this->getValue($innerEntityReflection, substr($paramName, $delimiterPosition + 1), $innerObject);
        }

        if (is_array($innerObject)) {
            //je to entita?
            $delimiterPosition = (int)strpos($paramName, '.');

            return $this->serviceLocator->get(DTOArrayBuilder::class)->getValue(
                substr($paramName, $delimiterPosition + 1),
                $innerObject,
            );
        }

        // Zde můžete zpracovat případ, kdy entita nemá požadovaný atribut
        // Například nastavit na null, použít výchozí hodnotu, nebo vyvolat výjimku
        return [null, false];
    }

    /**
     * @template T of \SabServis\DTOBuilder\DTO\AbstractDTO
     * @param class-string<T> $className
     * @return DTOBuilderConstructorClass<T>
     */
    public function getConstructorClass(string $className): DTOBuilderConstructorClass
    {
        if (!isset($this->constructorClassCache[$className])) {
            $this->constructorClassCache[$className] = new DTOBuilderConstructorClass($className);
        }

        /** @phpstan-ignore return.type */
        return $this->constructorClassCache[$className];
    }

    /**
     * @return DTOEntityBuilderSourceClass<object>
     */
    public function getSourceClass(object $entity): DTOEntityBuilderSourceClass
    {
        if (!isset($this->sourceClassCache[$entity::class])) {
            $this->sourceClassCache[$entity::class] = new DTOEntityBuilderSourceClass($entity::class);
        }

        return $this->sourceClassCache[$entity::class];
    }

    /**
     *
     * @template T of object
     * @param DTOEntityBuilderSourceClass<T> $entityReflection
     * @return object|array<mixed>|null
     */
    private function getInnerEntity(
        DTOEntityBuilderSourceClass $entityReflection,
        string $paramName,
        mixed $entity,
    ): null|object|array {
        if (str_contains($paramName, '.')) {
            $delimiterPosition = (int)strpos($paramName, '.');
            $firstLevelPropertyName = substr($paramName, 0, $delimiterPosition);
            $innerObject = $this->getValue($entityReflection, $firstLevelPropertyName, $entity)[0];

            if (is_object($innerObject) || is_array($innerObject)) {
                return $innerObject;
            }
        }

        return null;
    }
}
