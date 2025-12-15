<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeRecord;
use Illuminate\Support\Facades\Auth;

class TimeRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = TimeRecord::where('user_id', Auth::id())
        ->orderBy('recorded_at', 'desc')
        ->paginate(30);
    }

    public function store(Request $request)
    {
        $request->validate([ 
            'type' => 'required|in:entrada,intervalo_inicio,intervalo_fim,saida',
            'location' => 'nullable|string',
            'observation' => 'nullable|string',
        ]);

        $record = TimeRecord::create([
            'user_id' => Auth::id(),
            'recorded_at' => now(),
            'type' => $request->type, // <--- Aqui também: $request
            'location' => $request->location,
            'observation' => $request->observation
        ]);

        return response()->json([
            'message' => 'Ponto registrado com sucesso!',
            'data' => $record
        ], 201);
    }

    
    public function show(string $id)
    {
        $record = TimeRecord::where('user_id', Auth::id())->findOrFail($id);

        return response()->json($record);
    }



    public function update(Request $request, string $id)
    {
        $record = TimeRecord::where('user_id', Auth::id())
        ->findOrfail($id);

        $request->validade([
            'type' => 'in:entrada,intervalo_inicio,intervalo_fim,saida',
            'location' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
        ]);

        $record->update($request->all());

        return response()->json([
            'message' => 'Registro atualizado com sucesso',
            'record' => $record,
        ]);
    }


    public function destroy(string $id)
    {
        $record = TimeRecord::where('user_id', Auth::id())->findOrFail($id);
        $record->delete();

        return response()->json(['message' => 'Registro removido']);
    }
}
