<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sport;

class SportController extends Controller
{

    

    public function index(Request $request)
    {

        $page_title = 'Sports';
        $sports = Sport::all();
        
        return view('admin.sport.index', compact('page_title', 'sports'));
    }

    public function create(Request $request)
    {

        $page_title = 'Ajouter Sport';

        return view('admin.sport.create', compact('page_title'));
    }

    public function post(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'name_fr' => 'required',
            'icon' => 'required',
        ]);
        $sport = new Sport();
        $sport->name = $request->request->get('name');
        $sport->name_fr = $request->request->get('name_fr');
        $sport->icon = $this->store_image('path', $request->files->get('icon'));
        $sport->save();


        $notify[] = ['success', 'Create Successfully'];
        return back()->withNotify($notify);
    }

    public function edit(Request $request, $id)
    {

        $page_title = 'Editer Pronostics';
        $sport = Sport::find($id);

        return view('admin.sport.edit', compact('page_title', 'sport'));
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required',
            'name_fr' => 'required',
        ]);
        $sport = Sport::find($id);
        $data = $request->request->all();
        $sport->name = $request->request->get('name');
        $sport->name_fr = $request->request->get('name_fr');
        $sport->icon = $request->files->get('icon') ? $this->store_image('path', $request->files->get('icon'), $sport->icon) : $sport->icon;
        $sport->save();


        $notify[] = ['success', 'Update Successfully'];
        return back()->withNotify($notify);
    }

    protected function store_image($key, $image, $old_image = null)
    {
        $path = config('constants.sport.' . $key);
        return upload_image($image, $path, null, $old_image, null);
    }

}
