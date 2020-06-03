<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Eleccion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\JwtAuth;

class EleccionController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['only'=>['store','update','setinactive']]);
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
                'nombre' => 'required',
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
                    'message' => 'Los datos no han sido guardados.'
                ];
            }else {
                $eleccion = new Eleccion();
                $eleccion->nombre=$params_array['nombre'];
                $eleccion->fecha_inicio=$params_array['fecha_inicio'];
                $eleccion->fecha_fin=$params_array['fecha_fin'];
                $eleccion->hora_inicio=$params_array['hora_inicio'];
                $eleccion->hora_fin=$params_array['hora_fin'];
                $eleccion->descripcion=$params_array['descripcion'];
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

            //Quitar lo que no se va a actualizar
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

    public function setinactive($id)
    {
        $affected = DB::update('update elecciones set estado = 0 where id_eleccion = :id', ['id' => $id]);

        return \response()->json([
            'code' => 200,
            'status' => 'success',
            'affected' => $affected
        ]);
    }

    public function candidatos($id_eleccion){
        return $candidatos = Eleccion::with('candidatos')->where('id_eleccion', $id_eleccion)->get();
    }
}