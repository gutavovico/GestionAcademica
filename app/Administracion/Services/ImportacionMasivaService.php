<?php

namespace App\Administracion\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class ImportacionMasivaService
{
    private function leerCSV(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $fh = fopen($path, 'r');
        if (!$fh) return ['error' => 'No se pudo leer el archivo'];

        // Detectar delimitador por primera línea
        $sample = fgets($fh) ?: '';
        $delim = (substr_count($sample, ';') > substr_count($sample, ',')) ? ';' : ',';
        // Reset cursor
        fseek($fh, 0);

        // Si viene en ISO-8859-1 (común en CSV de Excel), convertir a UTF-8 al vuelo
        $rows = [];
        $headers = [];
        $rowNum = 0;
        while (($data = fgetcsv($fh, 0, $delim)) !== false) {
            $rowNum++;
            // Normalizar encoding
            $data = array_map(function ($v) {
                $v = (string) $v;
                if ($v === '') return '';
                $enc = mb_detect_encoding($v, ['UTF-8','ISO-8859-1','Windows-1252'], true) ?: 'UTF-8';
                return $enc === 'UTF-8' ? $v : mb_convert_encoding($v, 'UTF-8', $enc);
            }, $data);
            if ($rowNum === 1) {
                $headers = array_map(function ($h) { return strtolower(trim($h)); }, $data);
                continue;
            }
            if (count(array_filter($data, fn($x)=>$x!==''))===0) continue; // saltar filas vacías
            $row = [];
            foreach ($headers as $i => $h) { $row[$h] = $data[$i] ?? null; }
            $rows[] = $row;
        }
        fclose($fh);
        return ['rows' => $rows, 'headers' => $headers, 'delim' => $delim];
    }

    public function importarUsuarios(UploadedFile $file): array
    {
        $parsed = $this->leerCSV($file);
        if (isset($parsed['error'])) return $parsed;
        $rows = $parsed['rows'] ?? [];
        $ok = 0; $errores = [];
        foreach ($rows as $i => $r) {
            $linea = $i + 2; // considerando cabecera
            $nombre = trim((string)($r['nombre'] ?? ''));
            $correo = trim((string)($r['correo'] ?? ''));
            $telefono = trim((string)($r['telefono'] ?? ''));
            $contrasena = (string)($r['contrasena'] ?? 'temporal123');
            $rolNombre = trim((string)($r['rol'] ?? ''));
            if ($nombre === '' || $correo === '' || $rolNombre === '') { $errores[] = ['linea'=>$linea,'error'=>'Faltan campos obligatorios']; continue; }
            $rol = DB::table('roles')->where('nombre', $rolNombre)->first();
            if (!$rol) { $errores[] = ['linea'=>$linea,'error'=>'Rol no válido: '.$rolNombre]; continue; }
            try {
                // upsert por correo
                $existe = DB::table('usuario')->where('correo', $correo)->first();
                if ($existe) {
                    DB::table('usuario')->where('id_usuario', $existe->id_usuario)->update([
                        'nombre' => $nombre,
                        'telefono' => $telefono,
                        'contrasena' => $contrasena,
                        'id_rol' => $rol->id_rol,
                        'estado' => true,
                    ]);
                } else {
                    DB::table('usuario')->insert([
                        'nombre' => $nombre,
                        'correo' => $correo,
                        'telefono' => $telefono,
                        'contrasena' => $contrasena,
                        'id_rol' => $rol->id_rol,
                        'estado' => true,
                    ]);
                }
                $ok++;
            } catch (\Throwable $e) {
                $errores[] = ['linea'=>$linea,'error'=>$e->getMessage()];
            }
        }
        return ['ok'=>$ok,'errores'=>$errores,'total'=>count($rows)];
    }

    public function importarMaterias(UploadedFile $file): array
    {
        $parsed = $this->leerCSV($file);
        if (isset($parsed['error'])) return $parsed;
        $rows = $parsed['rows'] ?? [];
        $ok = 0; $errores = [];
        foreach ($rows as $i=>$r) {
            $linea = $i+2; $sigla=trim((string)($r['sigla']??'')); $nombre=trim((string)($r['nombre']??''));
            $semestre = trim((string)($r['semestre']??'')); $creditos = (int)($r['creditos']??0); $horas = (int)($r['horas_semana']??0);
            if ($sigla===''||$nombre===''){ $errores[]=['linea'=>$linea,'error'=>'Sigla y nombre requeridos']; continue; }
            try{
                $ex = DB::table('materia')->where('sigla',$sigla)->first();
                if($ex){ DB::table('materia')->where('id_materia',$ex->id_materia)->update(['nombre'=>$nombre,'semestre'=>$semestre,'creditos'=>$creditos,'horas_semana'=>$horas]); }
                else { DB::table('materia')->insert(['sigla'=>$sigla,'nombre'=>$nombre,'semestre'=>$semestre,'creditos'=>$creditos,'horas_semana'=>$horas]); }
                $ok++;
            }catch(\Throwable $e){ $errores[]=['linea'=>$linea,'error'=>$e->getMessage()]; }
        }
        return ['ok'=>$ok,'errores'=>$errores,'total'=>count($rows)];
    }

    public function importarGrupos(UploadedFile $file): array
    {
        $parsed = $this->leerCSV($file);
        if (isset($parsed['error'])) return $parsed;
        $rows = $parsed['rows'] ?? [];
        $ok = 0; $errores = [];
        foreach ($rows as $i=>$r) {
            $linea = $i+2; $nombre=trim((string)($r['nombre']??'')); $turno=trim((string)($r['turno']??'')); $cap=(int)($r['capacidad_max']??0);
            if ($nombre===''){ $errores[]=['linea'=>$linea,'error'=>'Nombre requerido']; continue; }
            try{
                $ex = DB::table('grupo')->where('nombre',$nombre)->first();
                if($ex){ DB::table('grupo')->where('id_grupo',$ex->id_grupo)->update(['turno'=>$turno,'capacidad_max'=>$cap]); }
                else { DB::table('grupo')->insert(['nombre'=>$nombre,'turno'=>$turno,'capacidad_max'=>$cap]); }
                $ok++;
            }catch(\Throwable $e){ $errores[]=['linea'=>$linea,'error'=>$e->getMessage()]; }
        }
        return ['ok'=>$ok,'errores'=>$errores,'total'=>count($rows)];
    }
}

