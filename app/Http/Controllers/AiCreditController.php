<?php

namespace App\Http\Controllers;

use App\Models\AiCredit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AiCreditController extends Controller
{
    public function index()
    {
        $credits = AiCredit::with('updater')
            ->orderBy('service')
            ->get()
            ->map(function ($credit) {
                return [
                    'id' => $credit->id,
                    'service' => $credit->service,
                    'total_credits' => $credit->total_credits,
                    'used_credits' => $credit->used_credits,
                    'remaining_credits' => $credit->remaining_credits,
                    'billing_period_start' => $credit->billing_period_start,
                    'billing_period_end' => $credit->billing_period_end,
                    'notes' => $credit->notes,
                    'updated_by' => $credit->updater ? $credit->updater->name : null,
                    'updated_at' => $credit->updated_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('AICredits/Index', [
            'credits' => $credits,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|in:kling,midjourney,suno',
            'total_credits' => 'required|integer|min:0',
            'used_credits' => 'required|integer|min:0',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after:billing_period_start',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['updated_by'] = auth()->id();

        AiCredit::create($validated);

        return back()->with('success', 'Créditos de IA agregados correctamente');
    }

    public function update(Request $request, AiCredit $aiCredit)
    {
        $validated = $request->validate([
            'service' => 'required|in:kling,midjourney,suno',
            'total_credits' => 'required|integer|min:0',
            'used_credits' => 'required|integer|min:0',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after:billing_period_start',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['updated_by'] = auth()->id();

        $aiCredit->update($validated);

        return back()->with('success', 'Créditos actualizados correctamente');
    }

    public function destroy(AiCredit $aiCredit)
    {
        $aiCredit->delete();

        return back()->with('success', 'Créditos eliminados correctamente');
    }
}
