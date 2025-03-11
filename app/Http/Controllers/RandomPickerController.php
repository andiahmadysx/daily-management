<?php

namespace App\Http\Controllers;

use App\Models\RandomPicker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RandomPickerController extends Controller
{
    public function getData()
    {
        $data = RandomPicker::get();
        return response()->json($data);
    }

    public function index()
    {
        return view('pages.random-pickers.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $random = Auth::user()->randomPickers()->create($request->only('name'));
        return response()->json($random);
    }

    public function destroy(RandomPicker $randomPicker)
    {
        $randomPicker->delete();
        return response()->json([
            'message' => 'Data deleted successfully',
        ]);
    }
}
