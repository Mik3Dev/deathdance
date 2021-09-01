<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentResquest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $appointments = Appointment::all();
        return AppointmentResource::collection($appointments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppointmentResquest $request)
    {
        $request->validated();

        $appointment = Appointment::create([
            'user_id'               => $request->user()->id,
            'description'           => $request->description,
            'appointment_date'      => Carbon::create($request->appointment_date),
            'start_time'            => Carbon::createFromTimeString($request->start_time)->toTimeString(),
            'end_time'              => Carbon::createFromTimeString($request->start_time)->addHour()->toTimeString(),
        ]);

        return new AppointmentResource($appointment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Appointment $appointment)
    {
        if ($request->user()->id === $appointment->id) {
            return new AppointmentResource($appointment);
        }

        return response(null, Response::HTTP_FORBIDDEN);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(AppointmentResquest $request, Appointment $appointment)
    {
        if ($request->user()->id !== $appointment->id) {
            return response(null, Response::HTTP_FORBIDDEN);
        }

        $request->validated();
        $appointment->description = $request->input('description', $appointment->description);
        $appointment->appointment_date = Carbon::create($request->appointment_date);
        $appointment->start_time = Carbon::createFromTimeString($request->start_time)->toTimeString();
        $appointment->end_time = Carbon::createFromTimeString($request->start_time)->addHour()->toTimeString();
        $appointment->save();

        return new AppointmentResource($appointment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Appointment $appointment)
    {
        if ($request->user()->id !== $appointment->id) {
            return response(null, Response::HTTP_FORBIDDEN);
        }

        $appointment->delete();

        return new AppointmentResource($appointment);
    }
}
