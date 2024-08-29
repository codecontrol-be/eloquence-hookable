<?php

use Kahlan\Plugin\Double;
use Sofa\Hookable\Builder;

describe('Sofa\Hookable\Builder', function () {

    beforeEach(function () {
        $connection = Double::instance(['implements' => 'Illuminate\Database\ConnectionInterface']);
        $queryGrammar = Double::instance(['extends' => 'Illuminate\Database\Query\Grammars\Grammar']);
        $processor = Double::instance(['extends' => 'Illuminate\Database\Query\Processors\Processor']);
        
        $query = Double::instance(['extends' => 'Illuminate\Database\Query\Builder', 'args' => [$connection, $queryGrammar, $processor]]);
        $this->builder = new Builder($query);
    });

    it('fallbacks to base builder for prefixed columns', function () {
        allow($this->builder)->toReceive('callParent')->andReturn(true);

        $this->builder->where('table.column', '=', 'value');

        expect($this->builder)->toReceive('callParent')->with('where', [
            "column" => "table.column",
            "operator" => "=",
            "value" => "value",
            "boolean" => "and"
        ]);
    });

    it('calls hook defined on the model', function () {
        $model = Double::instance();
        expect($model)->toReceive('queryHook');
        allow($this->builder)->toReceive('getModel')->andReturn($model);
        $this->builder->select(['column', 'value']);
    });
});