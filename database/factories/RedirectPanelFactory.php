<?php

namespace Backendprogramer\RedirectPanel\Database\Factories;

use Backendprogramer\RedirectPanel\Models\RedirectPanel;
use Illuminate\Database\Eloquent\Factories\Factory;

class RedirectPanelFactory extends Factory
{
    protected $model = RedirectPanel::class;

    public function definition()
    {
        return [
            'group_id' => $this->faker->randomNumber(),
            'from_path' => $this->faker->url,
            'to_path' => $this->faker->url,
            'type' => (string) $this->faker->numberBetween(301, 303),
        ];
    }
}
