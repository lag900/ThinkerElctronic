<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Services\InvoiceNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\Jobs\SendInvoiceEmailJob;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'order.customer'])->latest();

        if ($search = $request->search) {
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        return \Inertia\Inertia::render('Admin/Invoices', [
            'invoices' => $query->paginate(20),
            'customers' => \App\Models\Customer::orderBy('name')->get(),
            'filters' => $request->only('search')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'subtotal' => 'required|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'status' => 'required|in:paid,pending,cancelled,partial',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $baseTotal = floatval($validated['subtotal']) + floatval($validated['shipping'] ?? 0);
        
        $invoice = Invoice::create([
            'customer_id' => $validated['customer_id'],
            'invoice_number' => InvoiceNumberGenerator::generate(),
            'subtotal' => $validated['subtotal'],
            'shipping' => $validated['shipping'] ?? 0,
            'total' => $baseTotal,
            'amount_paid' => $validated['amount_paid'],
            'currency' => 'EGP',
            'status' => $validated['status'],
            'secure_token' => Str::random(32),
            'notes' => $validated['notes'],
        ]);

        return redirect()->back()->with('success', 'Invoice created successfully.');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:paid,pending,cancelled,partial'
        ]);

        $invoice->update($validated);

        return redirect()->back()->with('success', 'Invoice updated successfully.');
    }
    /**
     * Generate or regenerate invoice for an order.
     */
    public function generate(Request $request, Order $order)
    {
        $order->load(['items.product', 'customer']);

        // Check if invoice exists
        $invoice = $order->invoice;

        if (!$invoice) {
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => InvoiceNumberGenerator::generate(),
                'subtotal' => $order->subtotal,
                'shipping' => $order->delivery_price ?? 0,
                'total' => $order->total,
                'currency' => 'EGP',
                'status' => 'paid',
                'secure_token' => Str::random(32),
            ]);
        }

        // Generate PDF
        $fileName = 'invoices/' . $invoice->invoice_number . '.pdf';
        $html = view('pdf.invoice', compact('order', 'invoice'))->render();

        // Ensure directory exists
        if (!Storage::disk('public')->exists('invoices')) {
            Storage::disk('public')->makeDirectory('invoices');
        }

        $pdfPath = storage_path('app/public/' . $fileName);

        // Generate PDF strict settings using Snappy
        $pdf = PDF::loadView('pdf.invoice', compact('order', 'invoice'))
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10)
            ->setOption('encoding', 'UTF-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('dpi', 300)
            ->setOption('image-quality', 100)
            ->setOption('print-media-type', true)
            ->setOption('disable-smart-shrinking', true)
            ->save($pdfPath);

        $invoice->update([
            'pdf_path' => $fileName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully',
            'download_url' => route('admin.invoices.download', $invoice->secure_token),
            'invoice' => $invoice
        ]);
    }

    /**
     * Secure download for invoice.
     */
    public function download($token)
    {
        $invoice = Invoice::where('secure_token', $token)->firstOrFail();
        
        if (!$invoice->pdf_path || !Storage::disk('public')->exists($invoice->pdf_path)) {
            // Auto generate if missing
            $this->generate(request(), $invoice->order);
            $invoice->refresh();
        }

        return response()->download(Storage::disk('public')->path($invoice->pdf_path), "Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Send invoice to client email.
     */
    public function sendEmail(Request $request, Invoice $invoice)
    {
        $invoice->load('order.customer');
        
        if (!$invoice->order->customer->email) {
            return response()->json(['success' => false, 'message' => 'Customer has no email address.'], 422);
        }

        SendInvoiceEmailJob::dispatch($invoice);

        return response()->json(['success' => true, 'message' => 'Invoice sent to email successfully.']);
    }

    /**
     * Preview invoice HTML.
     */
    public function preview($id)
    {
        $order = Order::with(['items.product', 'customer'])->findOrFail($id);
        $invoice = $order->invoice ?? new Invoice([
            'invoice_number' => 'PREVIEW-000000',
            'subtotal' => $order->subtotal,
            'shipping' => $order->delivery_price ?? 0,
            'total' => $order->total,
            'currency' => 'EGP',
            'created_at' => now(),
        ]);

        return view('pdf.invoice', compact('order', 'invoice'));
    }
}
