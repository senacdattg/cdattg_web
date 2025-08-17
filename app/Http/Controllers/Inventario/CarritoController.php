<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario\Producto;
use App\Models\User;


class CarritoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $carrito = [];
        $total = 0; 
        return view('inventario.carrito.index', compact('carrito', 'total'));
    }

    public function agregar(Request $request)
    {
        
    }

    public function eliminar($id)
    {
        
    }

}
