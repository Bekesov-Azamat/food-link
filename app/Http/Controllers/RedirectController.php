<?php

namespace App\Http\Controllers;

use App\Services\RedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(
        Request $request,
        RedirectService $redirectService,
        string $shortCode,
    ): RedirectResponse {
        return $redirectService->redirect($shortCode, $request);
    }
}
