<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Company;
use Barryvdh\DomPDF\PDF;






class InvoiceController extends Controller
{
    public function show($orderId)
    {
        // Retrieve the order details
        $order = Order::findOrFail($orderId);
        
        // Retrieve the company details
        $company = Company::findOrFail($order->company_id);
        
        // Generate the invoice PDF using Dompdf
        $pdf = app('dompdf.wrapper')->loadView('backend.invoice.invoice', compact('order', 'company'));
        
        // Return the PDF for download or display
        return $pdf->stream('invoice.pdf');
    }

    public function download($id)
{
    $orderGroup = Order::where('id', $id)->get();
    $companyId = $orderGroup->first()->product->company_id;
    $company = Company::find($companyId);

    $pdf = app(PDF::class)->loadView('backend.invoice.invoice_pdf', compact('orderGroup', 'company'));

    return $pdf->download('invoice.pdf');
}

    
}
