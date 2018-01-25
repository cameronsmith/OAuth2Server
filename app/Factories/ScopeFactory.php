<?php namespace App\Factories;

class ScopeFactory extends Factory
{
    const TABLE_NAME = 'scopes';

    /**
     * Factory fields.
     *
     * @return array
     */
    protected static function factory() {
        return [
            'name' => 'general',
            'description' => 'A general scope with very little permissions',
        ];
    }
}