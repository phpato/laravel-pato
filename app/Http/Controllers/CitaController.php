<?php

namespace App\Http\Controllers;

use App\Cita;
use Illuminate\Http\Request;
use Validator;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("cita");
    }


    public function getCitas()
    {

        try{

            $citas = Cita::where("created_at", "!=", null)->orderBy("created_at","DESC")->get();
            //listar los creados al principio
            $citas = $citas->sortByDesc('created_at');

            return response()->json($citas);

        }catch (\Exception $e) {
            
            return response()->json(["error" => $e->getMessage()]);
        }

    }
    //funcion adaptadada para desplegar las horas dependiendo de la fecha
    public function evaluarCita(Request $request)
    {
        
        try {

            $validator = Validator::make($request->all(),
            [//reglas
                'start_date' => 'required|date',
                'email' => 'required|string|email|max:255|unique:citas'
            ],
            [//mensajes custom
                'start_date.required' => 'Debe ingresar una fecha para la cita',
                'start_date.date' => 'Debe ingresar una fecha valida',
                'email.required' => 'Debe ingresar un email',
                'email.email' => 'Debe ingresar una direccion de email valida',
                'email.unique' => "El email ingresado ya tiene una cita con la muerte"
            ]);
            
            if ($validator->fails()) {
    
                //return response()->json($validator->errors());
                return response()->json(["status" => 0, "errores" => $validator->errors()],200);
    
            }
            
            //horas disponibles
            $horas = [9,10,11,12,13,14,15,16,17,18];

            //devuelve las horas ocupadas
            $citas = Cita::select("hour_date")
                    ->distinct("hour_date")
                    ->where("start_date", $request->start_date)
                    ->pluck("hour_date")
                    ->toArray();
            
            
            if(count($citas) == 0){
                //se mandan todas las horas formateadas para vue-select
                $horasDisponibles = $this->arrayHorasDisponibles($horas);

                return response()->json(["status" =>1,"horasDisponibles" => $horasDisponibles]);
                
            }else{

                //pasando solo los valres de las citas
                $citasOcupadas = array_values($citas);
                //comparando discrepancias entre arrays
                $citasLibres = array_diff($horas, $citasOcupadas);
                //toda la diferencia sera disponible
                $horasDisponibles = $this->arrayHorasDisponibles($citasLibres);

                return response()->json(["status" =>1,"horasDisponibles" => $horasDisponibles]);

            }

        }catch (\Exception $e) {
          
            return response()->json(["error" => $e->getMessage()]);
        }

    }

    public function store(Request $request)
    {
        date_default_timezone_set('America/Santiago');
        //return response()->json(["status" =>1,"xd" => $request->data]);
        try {

            $validator = Validator::make($request->all(),
           
            [//reglas
                'start_date' => 'required|date',
                'hour_date' => 'required|integer',
                'email' => 'required|string|email|max:255'
            ],
            [//mensajes custom
                'start_date.required' => 'Debe ingresar una fecha para la cita',
                'start_date.date' => 'Debe ingresar una fecha valida',
                'hour_date.required' => 'Debe ingresar una hora',
                'hour_date.integer' => 'Debe ingresar un número',
                'email.required' => 'Debe ingresar un email',
                'email.email' => 'Debe ingresar una direccion de email valida',
            ]);
            
            if ($validator->fails()) {
    
                return response()->json($validator->errors());
    
            }
    
            $cita = Cita::create([
                "hour_date" => $request->hour_date,
                "start_date" => $request->start_date,
                "email" => $request->email
            ]);
    
            return response()->json(["status" =>1,"cita" => $cita]);
          
        }catch (\Exception $e) {
          
            return response()->json(["error" => $e->getMessage()]);
        }

    }


    public function destroy($id)
    {

        try{

            $cita = Cita::find($id);
            
            if($cita){

                $cita->delete();

                return response()->json(["status" =>1 , "cita"  => $cita]);

            }else{
                
                return response()->json(["status" =>0 , "cita"  => null ,"mensaje" => "Cita no encontrada en el sistema."]);

            } 


        }catch (\Exception $e) {
          
            return response()->json(["error" => $e->getMessage()]);
        }
        

    }

    private function arrayHorasDisponibles(array $horas){

        $formatoHoras = [];

        foreach($horas as $hora){
            
            $citaDisponible = [
                "hora" => $hora,
                "label" => "".$hora.":00"
            ];

            array_push($formatoHoras, $citaDisponible);

        }

        return $formatoHoras;
    }
}
