<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use function PHPSTORM_META\map;

class FavoriteController extends Controller
{
    public function index(){
        $favorites = Favorite::where('user_id', auth()->id())->get();
        return Inertia::render('Favorite/Index',[
            'favorites' => $favorites,
        ]);
    }

    public function store(Request $request){
        Favorite::create([
            'user_id' => auth()->id(),
            'movie_id' => $request->movie_id,
            'title' => $request->title,
            'poster_url' => $request->poster_url
        ]);
        $request->session()->flash('message', 'favorite saved!');
        return to_route('movie',['id' => $request->movie_id]);
    }
}
