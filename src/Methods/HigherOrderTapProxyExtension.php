<?php

declare(strict_types=1);

namespace NunoMaduro\Larastan\Methods;

use Illuminate\Support\HigherOrderTapProxy;
use NunoMaduro\Larastan\Concerns\HasBroker;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Dummy\DummyMethodReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\ObjectType;

final class HigherOrderTapProxyExtension implements MethodsClassReflectionExtension, BrokerAwareExtension
{
    use HasBroker;

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if ($classReflection->getName() !== HigherOrderTapProxy::class) {
            return false;
        }

        $templateTypeMap = $classReflection->getActiveTemplateTypeMap();

        $templateType = $templateTypeMap->getType('TClass');

        if (! $templateType instanceof ObjectType) {
            return false;
        }

        // dd($methodName, 141, $classReflection->getName(), $templateType, $templateType->hasMethod($methodName)->no(), ! $templateType->hasMethod('updatezz')->no(), $templateType->hasMethod('updatezz')->yes(), 3);
// dd($methodName, $templateType->hasMethod($methodName)->yes());
        return $templateType->hasMethod($methodName)->yes();
    }

    public function getMethod(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        /** @var ObjectType|null $templateType */
        $templateType = $classReflection->getActiveTemplateTypeMap()->getType('TClass');

        if ($templateType === null) {
            return new DummyMethodReflection($methodName);
        }

        $reflection = $templateType->getClassReflection();

        if ($reflection !== null) {
            return $reflection->getMethod($methodName, new OutOfClassScope());
        }

        return new DummyMethodReflection($methodName);
    }
}