<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Tests\Stubs;

final class Controller extends \Illuminate\Routing\Controller
{
    public function index()
    {
        return 'ok';
    }
}
