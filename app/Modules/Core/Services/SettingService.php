<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\Setting;

class SettingService
{
    public function __construct(private Setting $setting)
    {
    }

    public function set(string $group, string $key, $value): self
    {
        $this->setting->updateOrCreate(compact('group', 'key'), compact('value'));

        return $this;
    }

    public function remember(string $group, string $key, callable $callback) : mixed
    {
        if ($this->setting->newQuery()->group($group)->key($key)->exists()) {
            return $this->setting->where('group', $group)->where('key', $key)->first()?->value;
        }

        $value = $callback();
        $this->set($group, $key, $value);

        return $value;
    }

    public function get(string $group, string $key, $default = null)
    {
        return $this->setting->where('group', $group)->where('key', $key)->first()?->value ?? $default;
    }

    public function forget(string $group, string $key): self
    {
        $this->setting->where('group', $group)->where('key', $key)->delete();

        return $this;
    }
}
