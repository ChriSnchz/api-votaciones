<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Candidato;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CandidatoController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['only'=>['store','update','destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $candidatos = Candidato::with('eleccion')->get();

        return \response()->json([
            'code' => 200,
            'status' => 'success',
            'candidatos' => $candidatos
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
        //Recoger datos por post
        $json = $request->input('json', null);
        $params = \json_decode($json);
        $params_array = \json_decode($json, true);

        if (!empty($params_array)) {
            //Validar datos
            $validate = \Validator::make($params_array,[
                'nombre' => 'required',
                'apellido_paterno' => 'required',
                'apellido_materno' => 'required',
                'propuestas' => 'required',
                'id_eleccion' => 'required'
            ]);
            
            if ($validate->fails()) {
                //Si no llegan los datos correcto, regresar error
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el candidato, faltan datos.'
                ];
            } else {
                //Guardar candidato
                $candidato = new Candidato();
                $candidato->nombre = $params->nombre;
                $candidato->apellido_paterno = $params->apellido_paterno;
                $candidato->apellido_materno = $params->apellido_materno;
                $candidato->propuestas = $params->propuestas;
                $candidato->id_eleccion = $params->id_eleccion;

                $candidato->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'candidato' => $candidato
                ];
            }
            
        } else {
            //Estado de error
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente.'
            ];
        }
        //Devolver respuesta
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
        $candidato = Candidato::with('eleccion')->find($id);

        if (is_object($candidato)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'candidato' => $candidato
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El candidato no existe.'
            ];
        }
        
        return response()->json($data, $data['code']);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Buscar el candidato a eliminar
        $candidato = Candidato::find($id);

        //Comprobar si el candidato existe
        if (!empty($candidato)) {
            //Si existe, borrar
            $candidato->delete();
            //Devolver resultado
            $data = [
                'code' => 200,
                'status' => 'success',
                'candidato' => $candidato
            ];
        } else {
            //Si no existe, devolver error
            $data = [
                'code' => 40,
                'status' => 'error',
                'message' => 'El candidato no existe.'
            ];
        }
        
    }
}
