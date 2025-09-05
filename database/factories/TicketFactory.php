<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = [
            'Unable to login', 'Mobile Payment failed', 'Feature request: dark mode on mobile app',
            'App crashes on login', 'Account lock after password reset', 'Invoice items mismatch',
            'Cannot upload KYC document', 'OPT not received on phone number', 'Server error on invoice update'
        ];
        return [
            'subject' => $this->faker->randomElement($subjects),
            'status' => $this->faker->randomElement(['open','in_progress', 'resolved']),
            'body' => $this->faker->paragraph(3,true),
            'note' => $this->faker->boolean(40) ? $this->faker->sentence() : null,
            'category' => null,
            'explanation' => null,
            'confidence' => null
        ];
    }
}
