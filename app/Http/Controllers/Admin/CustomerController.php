<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Customer::query()
            ->withCount('invoices')
            ->withSum('invoices as total_invoiced', 'total')
            ->withSum('invoices as total_paid', 'amount_paid');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
        }

        $customers = $query->latest()->paginate(20)->through(function ($c) {
            $c->total_debt = max(0, ($c->total_invoiced ?? 0) - ($c->total_paid ?? 0));
            return $c;
        });

        $stats = [
            'total_customers' => \App\Models\Customer::count(),
            'total_debt' => \App\Models\Invoice::sum(\Illuminate\Support\Facades\DB::raw('total - amount_paid'))
        ];

        return \Inertia\Inertia::render('Admin/Customers', [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => $request->only('search')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => 'client',
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            ]);
            $validated['user_id'] = $user->id;
        }

        \App\Models\Customer::create($validated);

        return redirect()->back()->with('success', 'Customer added successfully');
    }

    public function show($id)
    {
        $customer = \App\Models\Customer::with('invoices.order')->findOrFail($id);
        
        $customer->total_invoiced = $customer->invoices->sum('total');
        $customer->total_paid = $customer->invoices->sum('amount_paid');
        $customer->total_debt = max(0, $customer->total_invoiced - $customer->total_paid);

        return \Inertia\Inertia::render('Admin/CustomerShow', [
            'customer' => $customer,
        ]);
    }
}
