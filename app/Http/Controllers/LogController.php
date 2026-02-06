<?php

namespace App\Http\Controllers;

use Response;

/**
 * Displays the application log files
 *
 * Provides a way for the admins to access the log files of the application
 * by putting '/log' in the URL.
 *
 * @author Adam Gleisner amg295@cornell.edu
 * @author Jeremy Miller jdm389@cornell.edu
 */
class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (session()->get('isTester')) {
            $logs = scandir('../storage/logs');

            return view('logfiles', ['logs' => $logs]);
        }

        return redirect()->route('index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (session()->get('isTester')) {
            return Response::make(file_get_contents("../storage/logs/$id"), 200, [
                'Content-Type' => 'text',
                'Content-Disposition' => 'inline; filename="'.$id.'"',
            ]);
        }

        return redirect()->route('index');
    }
}
