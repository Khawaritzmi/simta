<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDatabaseTaController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $query = trim((string) $request->query('q'));
        $records = DB::table('thesis_title_databases')
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($nested) use ($query) {
                    $nested
                        ->where('title', 'like', '%'.$query.'%')
                        ->orWhere('nim', 'like', '%'.$query.'%')
                        ->orWhere('student_name', 'like', '%'.$query.'%')
                        ->orWhere('supervisor_1', 'like', '%'.$query.'%')
                        ->orWhere('supervisor_2', 'like', '%'.$query.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.database-ta.index', [
            'records' => $records,
            'query' => $query,
            'editing' => null,
        ]);
    }

    public function edit(int $record): View
    {
        $this->ensureAdmin();

        $editing = DB::table('thesis_title_databases')->where('id', $record)->first();

        abort_if(! $editing, 404);

        $records = DB::table('thesis_title_databases')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.database-ta.index', [
            'records' => $records,
            'query' => '',
            'editing' => $editing,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $this->validated($request);

        DB::table('thesis_title_databases')->updateOrInsert(
            [
                'nim' => $validated['nim'],
                'title' => $validated['title'],
            ],
            $validated + [
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        return redirect()->route('admin.database-ta')->with('status', 'Data DELTA-MAT berhasil disimpan.');
    }

    public function update(Request $request, int $record)
    {
        $this->ensureAdmin();

        $validated = $this->validated($request);

        DB::table('thesis_title_databases')->where('id', $record)->update($validated + [
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.database-ta')->with('status', 'Data DELTA-MAT berhasil diperbarui.');
    }

    public function destroy(int $record)
    {
        $this->ensureAdmin();

        DB::table('thesis_title_databases')->where('id', $record)->delete();

        return redirect()->route('admin.database-ta')->with('status', 'Data DELTA-MAT berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'submission_date' => ['nullable', 'max:255'],
            'phone' => ['nullable', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'nim' => ['required', 'max:255'],
            'student_name' => ['required', 'max:255'],
            'title' => ['required', 'max:2000'],
            'supervisor_1' => ['nullable', 'max:255'],
            'supervisor_1_nip' => ['nullable', 'max:255'],
            'supervisor_2' => ['nullable', 'max:255'],
            'supervisor_2_nip' => ['nullable', 'max:255'],
            'document_url' => ['nullable', 'max:255'],
        ]);
    }

    private function ensureAdmin(): void
    {
        abort_if(Auth::user()->role !== 'admin', 403);
    }
}
