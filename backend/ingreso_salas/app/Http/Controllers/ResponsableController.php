<?php
    namespace App\Http\Controllers;

    use App\Models\Responsable;
    use Illuminate\Http\Request;
    
    class ResponsableController extends Controller
    {
        public function index()
        {
            return response()->json(Responsable::all());
        }
    }
    