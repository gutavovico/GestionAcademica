<?php

namespace App\Administracion\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Administracion\Services\ImportacionMasivaService;
use App\Support\BitacoraLogger;

class ImportacionMasivaController extends Controller
{
    public function __construct(private ImportacionMasivaService $service) {}

    public function vista()
    {
        return view('administracion.importar');
    }

    public function importar(Request $request)
    {
        $v = Validator::make($request->all(), [
            'entidad' => 'required|in:usuarios,materias,grupos',
            'archivo' => 'required|file',
        ]);
        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }
        $entidad = $request->input('entidad');
        $file = $request->file('archivo');
        $res = match($entidad){
            'usuarios' => $this->service->importarUsuarios($file),
            'materias' => $this->service->importarMaterias($file),
            'grupos'   => $this->service->importarGrupos($file),
        };
        BitacoraLogger::log('Importación CSV', strtoupper($entidad).': OK '.$res['ok'].' / Total '.$res['total']);
        return back()->with('resultado', $res)->with('entidad',$entidad);
    }
}
