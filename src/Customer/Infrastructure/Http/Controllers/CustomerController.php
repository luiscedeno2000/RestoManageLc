<?php

namespace Src\Customer\Infrastructure\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class CustomerController 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('index');
    }

    public function create()
    {
        return view('customer.create'); // TODO: es vuejs
    }

    public function store(Request $request) 
    {
        return view('customer.store'); // TODO: es vuejs
    }

    public function show($id)
    {
        dd('show');
    }

    public function edit($id)
    {
        return view('customer.edit'); // TODO: es vuejs
    }

    public function update(Request $request, $id)
    {
        return view('customer.update'); // TODO: es vuejs
    }

    public function destroy($id)
    {
        return view('customer.destroy'); // TODO: es vuejs
    }

    public function test()
    {
        dd('test');
    }
}