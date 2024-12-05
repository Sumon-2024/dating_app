<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{

    public function index()
    {
        $permissions = Role::latest()->paginate(10);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|min:3|unique:permissions,name|max:255',
            ]);

            Role::create([
                'name' => $request->input('name'),
            ]);

            DB::commit();

            return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating permission: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function edit($id)
    {
        $permission = Role::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $permission = Role::findOrFail($id);
            $request->validate([
                'name' => 'required|string|min:3|max:255|unique:permissions,name,' . $id,
            ]);

            $permission->update([
                'name' => $request->input('name'),
            ]);

            DB::commit();

            return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating permission with ID ' . $id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function destroy($id)
    {
        try {

            $permission = Role::findOrFail($id);
            $permission->delete();

            return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting permission with ID ' . $id . ': ' . $e->getMessage());
            return redirect()->route('permissions.index')->with('error', 'An error occurred while deleting the permission.');
        }
    }
}
