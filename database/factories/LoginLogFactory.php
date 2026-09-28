<?php

namespace Database\Factories;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoginLog>
 */
class LoginLogFactory extends Factory
{
    protected $model = LoginLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'opd_id' => null,
            'action' => 'login',
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
            'platform' => fake()->randomElement(['Windows 11', 'Android 14', 'iOS 17', 'macOS Sonoma']),
            'browser' => fake()->randomElement(['Chrome 122', 'Firefox 120', 'Edge 122', 'Safari 17']),
            'city' => 'Kota Ternate',
            'region' => 'Maluku Utara',
            'country' => 'Indonesia',
            'location' => 'Kota Ternate, Maluku Utara, Indonesia',
            'latitude' => 0.7893,
            'longitude' => 127.3871,
            'logged_at' => now(),
        ];
    }

    /**
     * State for logout action.
     */
    public function logout(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'logout',
        ]);
    }

    /**
     * State for mobile device.
     */
    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'mobile',
            'platform' => 'Android 14',
            'browser' => 'Chrome Mobile',
        ]);
    }

    /**
     * State for desktop device.
     */
    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'desktop',
            'platform' => 'Windows 11',
            'browser' => 'Chrome 122',
        ]);
    }
}
