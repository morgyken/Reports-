<?php

namespace Ignite\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Ignite\Evaluation\Entities\Investigations;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('reports::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('reports::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('reports::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('reports::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }


    /*
    * Generates the reports based on the type of report
    */
    public function generate()
    {
        dd("here");

        // $investigations = Investigations::with(['procedures'])->get();

        // Excel::create('Filename', function($excel) use($investigations) {

        //     $excel->sheet('Sheetname', function($sheet) use($investigations) {

        //         $sheet->freezeFirstRow();

        //         $sheet->row(1, [
        //             'procedure', 'quantity'
        //         ]);

        //         $investigations->each(function( $investigation ){
        //             $sheet->appendRow(2, array(
        //                 'appended', 'appended'
        //             ));
        //         });

        //     });

        // })->export('xls');
    }
}
