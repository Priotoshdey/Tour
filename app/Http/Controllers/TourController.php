<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{


    public function payment(Request $request)
    {
        Payment::create([
            'booking_id'=>$request->booking_id,
            'tour_id'=> $request->tour_id,
            'trx_id' => $request->trx_id,
            'total_amount' => $request->total_amount,
            'mobile'=>$request->mobile,

        ]);


        $booking = Booking::find($request->booking_id);
        $booking->paid = 1;
        $booking->save();

        $tour = Tour::find($request->tour_id);
        $tour->seat_number = $tour->seat_number - $booking->number_of_persons;
        $tour->save();

        return redirect()->route('home');
    }

    public function tour_check($id)
    {
        $tour = Tour::find($id);
        $bookings = Booking::where('tour_id', $tour->id)->get();

        $personCalc = 0;

        foreach ($bookings as $key => $booking) {
            $personCalc += $booking->number_of_persons;
        }


        return view('single-edit',[
            'tour' => $tour,
            'bookings' => $bookings,
            'totalPerson' => $personCalc,
        ]);


    }
    public function browse_all()
    {
        return view('browse');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'destination' => ['required'],
        ]);

        $images = [];

        foreach ($request->images as $image) {
            $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image_path =  $image->storeAs('images', $fileName,'public');

            array_push($images, $image_path);
        }
        $data['images'] = $images;

        $user_id = Auth::id();
        Tour::create([
            'destination'=>$request->destination,
            'cost_per_person' => $request->cost_per_person,
            'seat_number' =>$request->seat_number,
            'images' => json_encode($images),
            'description' => $request->description,
            'user_id' =>$user_id,
        ]);

        return back()
            ->with('success',"Data Succussfully Added");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tour = Tour::find($id);
        return view('single',['tour' =>$tour]);
    }


    public function booking(Request $request)
    {


        $request->validate([
            'mobile' => ['required'],
            'full_name' => ['required'],
            'number_of_persons' => ['required'],
        ]);

        if($request->number_of_persons > Tour::find($request->tour_id)->seat_number)
        {
            return back()->with("error", "Limit corsses");
        }
        $booking = Booking::create([
            'email' => $request->email,
            'full_name' => $request->full_name,
            'mobile' => $request->mobile,
            'number_of_persons'=>$request->number_of_persons,
            'tour_id' => $request->tour_id,
            'paid'=>0,
        ]);

        $tour = Tour::find($request->tour_id);

        $data = (object) [
            'booking_id'=> $booking->id,
            'number_of_persons'=>$request->number_of_persons,
            'cost_per_persons'=> $tour->cost_per_person,
            'sub_total' => $request->number_of_persons * $tour->cost_per_person,
            'tour_id' => $tour->id,
            'mobile' => $request->mobile,

        ];
        return view('booking', ['data'=>$data]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Http\Response
     */
    public function edit(Tour $tour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tour $tour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tour  $tour
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tour $tour)
    {
        //
    }


}
