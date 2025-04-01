<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\App;
use ToolMountain\LocalizedRoutes\ProvidesRouteParameters;

final class ModelWithMultipleRouteParameters extends BaseModel implements ProvidesRouteParameters
{
    protected $guarded = [];

    /**
     * Get the route parameters for this model.
     */
    public function getRouteParameters(?string $locale = null): array
    {
        return [
            $this->id,
            $this->attributes['slug'][$locale ?: App::getLocale()],
        ];
    }

    /**
     * Fake route model binding (avoid database for test purpose).
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return mixed
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this;
    }
}
