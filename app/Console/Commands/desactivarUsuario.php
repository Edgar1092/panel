<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\User;
use Illuminate\Support\Facades\Log;


class DesactivarUsuario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'desactivar:usuario';
        private $NAME_CONTROLLER = 'envio cron desactivar usuario';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desactivar usuarios cada 15 dias';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //


         try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $users = User::where('is_active',1)->get();
            $fecha_actual = date("Y-m-d");
            // var_dump($codigos);
                foreach($users as $user){
                    
                        $u =  User::find($user->id);
                        $u->is_active = 0;
                        $u->save();
                    
                }
                // echo $fecha_actual.' ';
            DB::commit(); // Guardamos la transaccion
       
        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error inesperado.',
            ], 500);
        }
       

    }
}
