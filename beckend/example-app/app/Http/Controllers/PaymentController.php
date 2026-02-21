<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::latest()->get();
        return response()->json($payments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cat = new Payment([
            "amount" => $request['amount'],
            "commission" => $request['commission'],
            "status" => $request['status'],
            "type" => $request['type'],
            "project_id" => $request['project_id'],
            "freelancer_id" => $request['freelancer_id'],
        ]);
        $cat->save();

         return response()->json([
            'success' => true,
            'message' => 'Платеж успешно создан'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        Payment::find($request->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно обновлен'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
         $payment->delete();
        return response()->json([
            'success' => true,
            'message' => 'Отзыв удален'
        ]);
    }
}
