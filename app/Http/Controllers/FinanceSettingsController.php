<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FinanceSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeSettings($request);

        return view('settings.finance', ['currencies' => Currency::query()->orderByDesc('is_default')->orderBy('code')->get(), 'taxes' => Tax::query()->orderByDesc('is_default')->orderBy('name')->get(), 'paymentMethods' => PaymentMethod::query()->orderBy('name')->get()]);
    }

    public function storeCurrency(Request $request): RedirectResponse
    {
        $this->authorizeSettings($request);
        $data = $request->validate(['code' => ['required', 'string', 'size:3', 'unique:currencies,code'], 'name' => ['required', 'string', 'max:100'], 'symbol' => ['required', 'string', 'max:10'], 'decimal_places' => ['required', 'integer', 'min:0', 'max:6']]);
        Currency::create(array_merge($data, ['code' => strtoupper($data['code']), 'exchange_rate' => 1, 'is_active' => true]));

        return back()->with('status', 'Mata uang ditambahkan.');
    }

    public function storeTax(Request $request): RedirectResponse
    {
        $this->authorizeSettings($request);
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'rate' => ['required', 'numeric', 'min:0', 'max:100']]);
        Tax::create(array_merge($data, ['is_active' => true]));

        return back()->with('status', 'Pajak ditambahkan.');
    }

    public function storePaymentMethod(Request $request): RedirectResponse
    {
        $this->authorizeSettings($request);
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);
        PaymentMethod::create(['name' => $data['name'], 'code' => Str::slug($data['name']), 'is_active' => true]);

        return back()->with('status', 'Metode pembayaran ditambahkan.');
    }

    private function authorizeSettings(Request $request): void
    {
        abort_unless($request->user()?->can('roles.manage'), 403);
    }
}
