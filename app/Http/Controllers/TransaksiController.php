<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $barangs     = Barang::all();
        $carts      = Transaksi::where("user_id", Auth::user()->id)->where("status",1)->where('type',2)->get();
        $checkouts   = Transaksi::where("user_id", Auth::user()->id)->where('status',2)->where('type',2)->get();
        $saldo      = Saldo::where("user_id", Auth::user()->id)->first();

        $total_cart = 0;
        $total_checkout = 0;

        foreach($carts as $cart){
        $total_cart += ($cart->barang->price * $cart->jumlah);
        }

        foreach($checkouts as $checkout){
            $total_cart += ($checkout->barang->price * $checkout->jumlah);
        }
        return view("transaksi")
        ->with('barangs',$barangs)
        ->with('carts', $carts)
        ->with('checkouts', $checkouts)
        ->with('total_cart',$total_cart)
        ->with('total_checkout',$total_checkout)
        ->with('saldo',$saldo);

    }

    public function topup_request(Request $request)
    {
        if($request->type == 1){
            $invoice_id = "SAL_" . Auth::user()->id . now()->timestamp;

            Transaksi::create([
                "user_id" => Auth::user()->id,
                "jumlah"  => $request->jumlah,
                "invoice_id" => $invoice_id,
                "type" => $request->type,
                "status" => 2
            ]);
            return redirect()->back()->with("status"," Top Up Saldo Sedang Di proses");
        }
    }
}
