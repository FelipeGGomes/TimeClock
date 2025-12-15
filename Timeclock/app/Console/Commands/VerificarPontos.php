<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TimeRecord;
use App\Mail\PontoInconsistente;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class VerificarPontos extends Command
{
    // O nome que você digitará no terminal para rodar
    protected $signature = 'ponto:verificar';
    protected $description = 'Verifica inconsistências nos pontos do dia anterior';

    public function handle()
    {
        // Vamos checar o dia de ONTEM (pois o dia de hoje ainda não acabou)
        $dataTeste = Carbon::today()->format('Y-m-d'); 
    
        $this->info("Verificando pontos para a data: $dataTeste");

        $users = User::all();

        foreach ($users as $user) {
            // Busca os registros usando a data de HOJE
            $records = TimeRecord::where('user_id', $user->id)
                        ->whereDate('recorded_at', $dataTeste) // <--- Use a variável nova aqui
                        ->pluck('type')
                        ->toArray();

            // Se o usuário não foi trabalhar (0 registros), ignoramos (ou marcamos falta, depende da regra)
            if (empty($records)) {
                continue;
            }

            $missing = [];

            // 1. Regra do Almoço: Se saiu pro almoço, tem que ter voltado
            if (in_array('intervalo_inicio', $records) && !in_array('intervalo_fim', $records)) {
                $missing[] = 'intervalo_fim';
            }

            // 2. Regra básica: Se entrou, tem que ter saído
            if (in_array('entrada', $records) && !in_array('saida', $records)) {
                $missing[] = 'saida';
            }

            // Caso reverso: Voltou do almoço mas não marcou a ida?
            if (!in_array('intervalo_inicio', $records) && in_array('intervalo_fim', $records)) {
                $missing[] = 'intervalo_inicio';
            }

            // Se encontrou erros, envia o e-mail
            if (!empty($missing)) {
                $this->error("Inconsistência encontrada para: " . $user->name);

                $this->info(" -> Enviando e-mail para: " . $user->email);

                Mail::to($user->email)->send(new PontoInconsistente($user, $dataTeste, $missing));
            }
        }

        $this->info('Verificação concluída.');
    }
}
