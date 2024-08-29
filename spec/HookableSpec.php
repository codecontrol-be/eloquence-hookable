<?php

use Kahlan\Plugin\Double;
use Sofa\Hookable\Hookable;

describe('Sofa\Hookable\Hookable', function () {

    it('resolves hooks in instance scope', function () {
        $parent = Double::classname();
        allow($parent)->toReceive('getAttribute')->andReturn('value');

        $hookableClass = Double::classname(['uses' => Hookable::class, 'extends' => $parent]);
        $hookableClass::hook('getAttribute', function ($next, $value, $args) {
            $this->instanceMethod();
        });

        $hookable = new $hookableClass;
        expect($hookable)->toReceive('instanceMethod');
        $hookable->getAttribute('attribute');
    });

    it('flushes all hooks with the flushHooks method', function () {
        $parent = Double::classname();

        $hookableClass = Double::classname(['uses' => Hookable::class, 'extends' => $parent]);
        $hookableClass::hook('method1', function ($next, $value, $args) {});
        $hookableClass::hook('method2', function ($next, $value, $args) {});

        $reflectedClass = new ReflectionClass($hookableClass);
        $reflectedProperty = $reflectedClass->getProperty('hooks');
        $reflectedProperty->setAccessible(true);

        $hooks = $reflectedProperty->getValue();
        expect($hooks[$reflectedClass->getName()])->toHaveLength(2);

        $hookableClass::flushHooks();

        $hooks = $reflectedProperty->getValue();
        expect($hooks)->toHaveLength(0);
    });

});
