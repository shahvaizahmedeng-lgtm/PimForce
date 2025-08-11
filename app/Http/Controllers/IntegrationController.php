<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $integrations = Auth::user()->integrations()->latest()->paginate(10);
        
        return view('integrations.index', compact('integrations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('integrations.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Integration $integration)
    {
        // Ensure user can only view their own integrations
        if ($integration->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('integrations.show', compact('integration'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Integration $integration)
    {
        // Ensure user can only delete their own integrations
        if ($integration->user_id !== Auth::id()) {
            abort(403);
        }
        
        $integration->delete();
        
        return redirect()->route('integrations.index')
            ->with('message', 'Integration deleted successfully!');
    }
}
