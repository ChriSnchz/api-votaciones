<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{
    public function register(Request $request){
        //Recoger datos
        $json = $request->input('json', null);
        $params = json_decode($json); //Objeto
        $params_array = \json_decode($json, true); //Array

        if (!empty($params) && !empty($params_array)) {
            //Limiar datos
            $params_array = array_map('trim', $params_array);

            //Validar datos
            $validate = \Validator::make($params_array,[
                'email' => 'required|email|unique:users',
                'nombre' => 'required|alpha',
                'apellido_paterno' => 'required|alpha',
                'apellido_materno' => 'required|alpha',
                'password' => 'required',
                'role' => 'ROLE_USER'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado.',
                    'errors' => $validate->errors()
                );
            }else {
                //Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                //Crear el usuario

                $user = new User();
                $user->email = $params_array['email'];
                $user->nombre = $params_array['nombre'];
                $user->apellido_paterno = $params_array['apellido_paterno'];
                $user->apellido_materno = $params_array['apellido_materno'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //Guardar el usuario
                $user->save();
                
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        }else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos',
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request){
        $jwtAuth = new \JwtAuth();

        //Recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = \json_decode($json, true);

        //Validar los datos
        $validate = \Validator::make($params_array,[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        }else {
            //Cifrar la password
            $pwd = hash('sha256', $params->password);

            //Devolver token
            $signup = $jwtAuth->signup($params->email, $pwd);

            if (!empty($params->getToken)) {
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }
        return response()->json($signup, 200);
    }

    public function update(Request $request){
        //Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {
            //Sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

            //Validar los datos
            $validate = \Validator::make($params_array, [
                'email' => 'required|email|unique:users,'.$user->sub,
                'nombre' => 'required|alpha',
                'apellido_paterno' => 'required|alpha',
                'apellido_materno' => 'required|alpha',
                'password' => 'required',
            ]);

            //Quitar campos que no se actualizaran
            unset($params_array['role']);

            //Actualizar usuario en db
            $user_update = User::where('email', $user->sub)->update($params_array);

            //Devolver array en rasultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array
            );

        }else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen.'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request){
        //Recoger datos de la peticion
        $img = $request->file('file0');

        //Validacion de imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:png,jpg,jpeg'
        ]);

        //Guardar imagen
        if (!$img || $validate->fails()) {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen.'
            );
        }else {
            $image_name = time().$img->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($img));

        //Devolver resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'img' => $image_name
            );
        }
        return \response()->json($data, $data['code']);
    }

    public function getImage($filename){
        //Comprobar si la imagen existe
        $isset = \Storage::disk('users')->exists($filename);

        //Enviar resultado
        if ($isset) {
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        }else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe.'
            );

            return \response()->json($data, $data['code']);
        }
    }

    public function detail($email){
        $user = User::find($email);

        if (is_object($user)) {
            $data = array(
                'code' => '200',
                'status' => 'success',
                'user' => $user
            );
        }else {
            $data = array(
                'code' => '404',
                'status' => 'error',
                'message' => 'El usuario no existe.'
            );
        }

        return \response()->json($data, $data['code']);
    }
}
