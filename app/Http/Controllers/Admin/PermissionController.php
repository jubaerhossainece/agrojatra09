<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PositionPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->isPresident(), 403, 'Only the president can manage permissions.');

        $positions   = PositionPermission::POSITIONS;
        $permissions = PositionPermission::PERMISSIONS;
        $assigned    = PositionPermission::all()
            ->groupBy('position')
            ->map(fn($items) => $items->pluck('permission')->all());

        return view('admin.permissions.index', compact('positions', 'permissions', 'assigned'));
    }

    public function update(Request $request)
    {
        abort_if(!auth()->user()->isPresident(), 403, 'Only the president can manage permissions.');

        $positions      = PositionPermission::POSITIONS;
        $permissionKeys = array_keys(PositionPermission::PERMISSIONS);

        DB::transaction(function () use ($request, $positions, $permissionKeys) {
            PositionPermission::truncate();

            foreach ($positions as $position) {
                foreach ($permissionKeys as $permission) {
                    if ($request->boolean("{$position}_{$permission}")) {
                        PositionPermission::create(compact('position', 'permission'));
                    }
                }
            }
        });

        foreach ($positions as $position) {
            Cache::forget("position_perms_{$position}");
        }

        return back()->with('success', 'Permissions updated successfully.');
    }
}
