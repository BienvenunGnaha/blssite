<?php

namespace App\Http\Controllers\Admin;

use App\Bookmaker;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminBookmakerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Bookmakers';
        $bms = Bookmaker::all();
        return view('admin.bookmaker.index', compact('page_title','bms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Edition Bookmaker';   
        return view('admin.bookmaker.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'photo' => 'required',
        ]);
        $sport = new Bookmaker();
        $sport->name = $request->request->get('name');
        $sport->photo = $this->store_image('path', $request->files->get('photo'));
        $sport->save();


        $notify[] = ['success', 'Create Successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bookmaker  $bookmaker
     * @return \Illuminate\Http\Response
     */
    public function show(Bookmaker $bookmaker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bookmaker  $bookmaker
     * @return \Illuminate\Http\Response
     */
    public function edit(Bookmaker $bookmaker)
    {
        $page_title = 'Edition Bookmaker';   
        return view('admin.bookmaker.edit', compact('page_title','bookmaker'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bookmaker  $bookmaker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bookmaker $bookmaker)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $sport = Bookmaker::find($bookmaker->id);
        $sport->name = $request->request->get('name');
        $sport->photo = $request->files->get('photo') ? $this->store_image('path', $request->files->get('photo'), $bookmaker->photo) : $bookmaker->photo;
        $sport->save();


        $notify[] = ['success', 'Update Successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bookmaker  $bookmaker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bookmaker $bookmaker)
    {
        
        $bookmaker->delete();
        
        $notify[] = ['success', 'Delete Successfully'];
        return back()->withNotify($notify);
    }

    protected function store_image($key, $image, $old_image = null)
    {
        $path = config('constants.bookmaker.' . $key);
        //dd($path);
        return upload_image($image, $path, null, $old_image, null);
    }
}
