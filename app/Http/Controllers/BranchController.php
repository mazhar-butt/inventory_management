<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with(['creator', 'manager'])->paginate(10);
        $users = User::select('id', 'name')->get();
        
        // Get only users with manager role for the dropdown
        $managerRole = Role::where('slug', 'manager')->first();
        $managers = $managerRole ? User::where('role_id', $managerRole->id)->select('id', 'name')->get() : collect();
        
        return Inertia::render('Branches/Index', [
            'branches' => $branches,
            'users' => $users,
            'managers' => $managers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $validated['is_active'] ?? true;

        Branch::create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
