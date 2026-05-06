<?php

namespace App\Http\Controllers;

use App\Models\ItaFiscalYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItaFiscalYearController extends Controller
{
    public function index()
    {
        $fiscalYears = ItaFiscalYear::query()
            ->withCount(['topics', 'subTopics', 'documents'])
            ->orderByDesc('year')
            ->paginate(20);

        return view('ita.fiscal-years.index', compact('fiscalYears'));
    }

    public function create()
    {
        return view('ita.fiscal-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2500', 'max:2700', 'unique:ita_fiscal_years,year'],
            'name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ItaFiscalYear::create([
            'year' => $validated['year'],
            'name' => $validated['name'] ?: 'ปีงบประมาณ ' . $validated['year'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('ita.fiscal-years.index')
            ->with('success', 'เพิ่มปีงบประมาณเรียบร้อยแล้ว');
    }

    public function edit(ItaFiscalYear $fiscalYear)
    {
        return view('ita.fiscal-years.edit', compact('fiscalYear'));
    }

    public function update(Request $request, ItaFiscalYear $fiscalYear)
    {
        $validated = $request->validate([
            'year' => [
                'required',
                'integer',
                'min:2500',
                'max:2700',
                Rule::unique('ita_fiscal_years', 'year')->ignore($fiscalYear->id),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $fiscalYear->update([
            'year' => $validated['year'],
            'name' => $validated['name'] ?: 'ปีงบประมาณ ' . $validated['year'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('ita.fiscal-years.index')
            ->with('success', 'แก้ไขปีงบประมาณเรียบร้อยแล้ว');
    }

    public function destroy(ItaFiscalYear $fiscalYear)
    {
        if (
            $fiscalYear->topics()->exists() ||
            $fiscalYear->subTopics()->exists() ||
            $fiscalYear->documents()->exists()
        ) {
            return redirect()
                ->route('ita.fiscal-years.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีหัวข้อหรือไฟล์เอกสารผูกอยู่');
        }

        $fiscalYear->delete();

        return redirect()
            ->route('ita.fiscal-years.index')
            ->with('success', 'ลบปีงบประมาณเรียบร้อยแล้ว');
    }
}
