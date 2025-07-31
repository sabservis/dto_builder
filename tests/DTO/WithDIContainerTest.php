<?php

namespace SabServis\DTOBuilder\Tests\DTO;

use PHPUnit\Framework\TestCase;
use SabServis\DTOBuilder\DTO\Builder\DTOArrayBuilder;
use SabServis\DTOBuilder\DTO\Builder\DTOEntityBuilder;
use SabServis\DTOBuilder\DTO\Builder\DTOMultiArrayBuilder;
use SabServis\DTOBuilder\DTO\Builder\DTOMultiEntityBuilder;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOArrayValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOBooleanValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTODatetimeValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTODefaultValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTODtoValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOEnumArrayValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTONumberValueFilter;
use SabServis\DTOBuilder\DTO\Builder\Filter\DTOStringValueFilter;
use SabServis\DTOBuilder\Helper\DIResolver;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validation;

class WithDIContainerTest extends TestCase
{
    protected Container $container;

    public function __construct() {
        parent::__construct();
        $container = new Container();
        $container->set(DTODefaultValueFilter::class, new DTODefaultValueFilter());
        $container->set(DTOEnumArrayValueFilter::class, new DTOEnumArrayValueFilter());
        $container->set(DTOBooleanValueFilter::class, new DTOBooleanValueFilter());
        $container->set(DTOStringValueFilter::class, new DTOStringValueFilter());
        $container->set(DTODatetimeValueFilter::class, new DTODatetimeValueFilter());
        $container->set(DTONumberValueFilter::class, new DTONumberValueFilter());
        $container->set(\DateTime::class, new \DateTime());

        $arrayBuilder = new DTOArrayBuilder(
            Validation::createValidatorBuilder()
                ->getValidator(),
            $container,
            new DIResolver($container),
        );
        $container->set(DTOArrayBuilder::class, $arrayBuilder);

        $multiArrayBuilder = new DTOMultiArrayBuilder(
            Validation::createValidatorBuilder()
                ->getValidator(),
            $container,
            new DIResolver($container),
        );
        $container->set(DTOMultiArrayBuilder::class, $multiArrayBuilder);

        $entityBuilder = new DTOEntityBuilder(
            Validation::createValidatorBuilder()
                ->getValidator(),
            $container,
            new DIResolver($container),
        );
        $container->set(DTOEntityBuilder::class, $entityBuilder);


        $multiEntityBuilder = new DTOMultiEntityBuilder(
            $entityBuilder,
            Validation::createValidatorBuilder()
                ->getValidator(),
            $container,
            new DIResolver($container),
        );
        $container->set(DTOMultiEntityBuilder::class, $multiEntityBuilder);

        $container->set(DTODtoValueFilter::class, new DTODtoValueFilter($arrayBuilder, $entityBuilder));
        $container->set(DTOArrayValueFilter::class, new DTOArrayValueFilter($multiArrayBuilder));

        $this->container = $container;
    }
}
