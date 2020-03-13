<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Eleccion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EleccionController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['exept'=>['index','show', 'activas']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $elecciones = Eleccion::all();

        return \response()->json([
            'code' => 200,
            'status' => 'success',
            'elecciones' => $elecciones
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Recoger datos
        $json = $request->input('json', null);
        $params_array = \json_decode($json, true);

        if (!empty($params_array)) {
            //Validar datos
            $validate = \Validator::make($params_array, [
                'nomre' => 'required',
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
                'descripcion' => 'required'
            ]);
            //Guardar
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Los datos no son correctos.'
                ];
            }else {
                $eleccion = new Eleccion();
                $eleccion->name=$params_array['nombre'];
                $eleccion->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'eleccion' => $eleccion
                ];
            }
        }else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Los datos no son correctos.'
            ];
        }
        //Devolver resultado
        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $eleccion = Eleccion::find($id);

        if (is_object($eleccion)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'eleccion' => $eleccion
            ];
        }else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La eleccion no existe.'
            ];
        }

        return \response()->json($data, $data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Recoger datos
        $json = $request->input('json', null);
        $params_array = \json_decode($json, true);

        if (!empty($params_array)) {
            //Validar datos
            $validate = \Validator::make($params_array, [
                'nombre' => 'required',
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
                'descripcion' => 'required'
            ]);

            //Unset
            unset($params_array['id']);
            unset($params_array['estado']);

            //Actualizar el registro
            $eleccion = Eleccion::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status' => 'success',
                'eleccion' => $params_array
            ];
        }else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Los datos no son correctos.'
            ];
        }

        return \response()->json($data,$data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activas()
    {
        $elecciones = DB::select('select * from elecciones where estado = :estado', ['estado' => 1]);

        return \response()->json([
            'code' => 200,
            'status' => 'success',
            'elecciones' => $elecciones
        ]);
    }

    public function inactivas()
    {
        $elecciones = DB::select('select * from elecciones where estado = :estado', ['estado' => 0]);

        return \response()->json([
            'code' => 200,
            'status' => 'success',
            'elecciones' => $elecciones
        ]);
    }
}
