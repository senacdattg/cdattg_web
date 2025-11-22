<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

abstract class InventarioController extends Controller
{
    
    // Constructor común para todos los controladores de inventario
    public function __construct()
    {
        $this->middleware('auth');
    }

    
    // Asignar IDs de usuario a un modelo antes de guardar
    protected function setUserIds($model, $isUpdate = false) : void
    {
        $userId = Auth::id();
        
        if (!$isUpdate) {
            $model->user_create_id = $userId;
        }
        
        $model->user_update_id = $userId;
    }

    
    // Respuesta JSON estándar para éxito
    protected function successResponse($message, $data = null, $status = 200) : JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    
    // Respuesta JSON estándar para error
    protected function errorResponse($message, $errors = null, $status = 400) : JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
