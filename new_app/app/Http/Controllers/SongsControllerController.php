<?php

namespace App\Http\Controllers;

use App\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
class SongsControllerController extends Controller
{
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $songs = Song::where('business_id', $business_id)
                        ->select('*');
            return Datatables::of($songs)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\SongsControllerController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".songs_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\SongsControllerController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_song_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('songs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get the full path to the public directory
        $publicPath = public_path('audio/songs');
        // Get the content of the public folder
        $folderContent = scandir($publicPath);
        // Filter out unwanted entries
        $filteredContent = array_diff($folderContent, ['.', '..']);
        // Create an array for the select dropdown
        $selectOptions = [];
        foreach ($filteredContent as $item) {$selectOptions[$item] = $item;}

         // Get all routes
         $routes = collect(Route::getRoutes());
         // Filter routes with non-null names
         $filteredRoutes = $routes->filter(function ($route) {
            return 
            isset($route->action['as']) 
            && !is_null($route->action['as']) 
            && !Str::startsWith($route->action['as'], 'log') 
            && !Str::startsWith($route->action['as'], 'passport') 
            && !Str::startsWith($route->action['as'], 'debugbar') 
            && !Str::startsWith($route->action['as'], 'install') 
            && !Str::startsWith($route->action['as'], 'password') 
            && !Str::startsWith($route->action['as'], 'register') 
            && !Str::startsWith($route->action['as'], 'payment') 
            && !Str::startsWith($route->action['as'], 'songs') 
            && !Str::startsWith($route->action['as'], 'roles') 
            && !Str::startsWith($route->action['as'], 'backup') 
            && !Str::startsWith($route->action['as'], 'manage') 
            && !Str::startsWith($route->action['as'], 'songs') 
            && !Str::startsWith($route->action['as'], 'songs') 
            ;
        });
         // Extract route names
         $routeNames = $filteredRoutes->pluck('action.as');
        return view('songs.create',compact('selectOptions','routeNames'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('tax_rate.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $input = $request->only(['song_name', 'song','routeSelect']);
            $business_id = $request->session()->get('user.business_id');
            Song::create([
                'song_name' => $input['song_name'],
                'path' => $input['song'],
                'nike_name' => $input['routeSelect'],
                'business_id'  => $business_id,
            ]);
            $output = ['success' => true,
                'msg' => __('songs.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $song = Song::where('business_id', $business_id)->find($id);

             // Get the full path to the public directory
            $publicPath = public_path('audio/songs');
            // Get the content of the public folder
            $folderContent = scandir($publicPath);
            // Filter out unwanted entries
            $filteredContent = array_diff($folderContent, ['.', '..']);
            // Create an array for the select dropdown
            $selectOptions = [];
            foreach ($filteredContent as $item) {$selectOptions[$item] = $item;}

             // Get all routes
         $routes = collect(Route::getRoutes());
         // Filter routes with non-null names
         $filteredRoutes = $routes->filter(function ($route) {
            return isset($route->action['as']) && !is_null($route->action['as']) && starts_with($route->action['as'], 'log');
        });
         // Extract route names
         $routeNames = $filteredRoutes->pluck('action.as');

            return view('songs.edit')
                ->with(compact('song','selectOptions','routeNames'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('tax_rate.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['song_name', 'song','routeSelect']);
                $business_id = $request->session()->get('user.business_id');
                $song = Song::where('business_id', $business_id)->findOrFail($id);
                $song->song_name = $input['song_name'];
                $song->nike_name = $input['routeSelect'];
                $song->path = $input['song'];
                $song->business_id = $business_id;
                $song->save();


                $output = ['success' => true,
                    'msg' => __('song.updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {   
                $business_id = request()->session()->get('user.business_id');
                    $song = Song::where('business_id', $business_id)->findOrFail($id);
                    $song->delete();
                    $output = ['success' => true,'msg' => __('song.deleted_success'),];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,'msg' => __('messages.something_went_wrong'),];
            }

            return $output;
        }
    }
}
