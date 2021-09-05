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
        $datetime = Carbon::create($request->appointment_datetime);
        $end_datetime = Carbon::create($datetime)->addHour();

        $appointment = Appointment::create([
            'user_id'               => $request->user()->id,
            'description'           => $request->description,
            'appointment_datetime'  => $datetime->toISOString(),
            'end_datetime'          => $end_datetime->toISOString(),
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
        if ($request->user()->id == $appointment->user_id) {
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
        if ($request->user()->id != $appointment->user_id) {
            return response(null, Response::HTTP_FORBIDDEN);
        }

        $datetime = Carbon::create($request->appointment_datetime);

        $request->validated();
        $appointment->description = $request->input('description', $appointment->description);
        $appointment->appointment_datetime = $datetime->toISOString();
        $appointment->end_datetime = $datetime->addHour()->toISOString();
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
        if ($request->user()->id != $appointment->user_id) {
            return response(null, Response::HTTP_FORBIDDEN);
        }

        $appointment->delete();

        return new AppointmentResource($appointment);
    }
}
