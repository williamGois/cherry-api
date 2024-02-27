<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use Laravel\Lumen\Routing\Controller as BaseController;

class MeetingController extends BaseController
{
    public function index()
    {
        $meetings = Meeting::all();
        return response()->json($meetings);
    }

    public function store(Request $request)
    {
        $meeting = new Meeting;
        $meeting->meeting_date = $request->dataReuniao;
        $meeting->start_time = $request->horarioInicio;
        $meeting->end_time = $request->horarioTermino;
        $meeting->users = $request->namesString;
        $meeting->title = $request->titulo;
        $meeting->description = $request->descricao;

        $meeting->save();

        return response()->json($meeting, 201);
    }
}
