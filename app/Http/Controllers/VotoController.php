<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Voto;

class VotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                'email' => 'required'|email,
                'id_candidato' => 'required',
            ]);
            //Guardar
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Los datos no han sido guardados.'
                ];
            }else {
                $voto = new Voto();
                $voto->email=$params_array['email'];
                $voto->id_candidato=$params_array['id_candidato'];
                $voto->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'voto' => $voto
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
        //
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
        //
    }

    public function getVotos(){
        $data = Voto::with('candidato')
        ->select('id_candidato', DB::raw('count(*) as total'))
        ->groupBy('id_candidato')
        ->get();

        return response()->json($data);
    }
}