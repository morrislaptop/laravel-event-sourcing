<?php

namespace Spatie\EventSourcing;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class Handlers
{
    /**
     * @param object|string $object
     * @param object $handler
     *
     * @return \Illuminate\Support\Collection|string[]
     */
    public static function find(object | string $object, object $handler): Collection
    {
        $eventName = is_object($object)
            ? $object::class
            : $object;

        $handlerClass = new ReflectionClass($handler);

        return collect($handlerClass->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED))
            ->filter(function (ReflectionMethod $method) use ($eventName) {
                $parameter = $method->getParameters()[0] ?? null;

                if (! $parameter) {
                    return false;
                }

                $type = $parameter->getType();

                if (! $type) {
                    return false;
                }

                /** @var \ReflectionNamedType[] $types */
                $types = match ($type::class) {
                    ReflectionUnionType::class => $type->getTypes(),
                    ReflectionNamedType::class => [$type],
                };

                return collect($types)
                    ->contains(fn (ReflectionNamedType $type) => $type->getName() === $eventName);
            })
            ->values()
            ->map(fn (ReflectionMethod $method) => $method->getName());
    }

    /**
     * @param object|string $event
     * @param object $handler
     *
     * @return \Illuminate\Support\Collection|string[][]
     */
    public static function list(object $handler): Collection
    {
        $handlerClass = new ReflectionClass($handler);

        $methods = $handlerClass->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);

        $handlers = [];

        foreach ($methods as $method) {
            $parameter = $method->getParameters()[0] ?? null;

            if (! $parameter) {
                continue;
            }

            $type = $parameter->getType();

            if (! $type) {
                continue;
            }

            /** @var \ReflectionNamedType[] $types */
            $types = match ($type::class) {
                ReflectionUnionType::class => $type->getTypes(),
                ReflectionNamedType::class => [$type],
            };

            foreach ($types as $type) {
                if (! is_subclass_of($type->getName(), ShouldBeStored::class)) {
                    continue;
                }

                $handlers[$type->getName()][] = $method->getName();
            }
        }

        return collect($handlers);
    }
}
