<?php

namespace App\Administracion\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Administracion\Services\PanelAdminService;

class PanelAdminController extends Controller
{
    private PanelAdminService $service;

    public function __construct(PanelAdminService $service)
    {
        $this->service = $service;
    }

    /**
     * Vista del panel administrativo general (CU20).
     */
    public function dashboard()
    {
        $user = Auth::user();
        $metrics = $this->service->metricas();
        return view('dashboard_admin', array_merge(['user' => $user], $metrics));
    }

    /**
     * Endpoint opcional para métricas en JSON.
     */
    public function metricas()
    {
        return response()->json($this->service->metricas(), 200);
    }
}

