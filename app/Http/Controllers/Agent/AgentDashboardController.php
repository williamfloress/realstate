<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Prop\HomeType;
use App\Models\Prop\Property;
use App\Models\Prop\Request as PropRequest;
use Illuminate\Support\Facades\Auth;

class AgentDashboardController extends Controller
{
    public function dashboard()
    {
        $agent = Auth::user();
        $propertyCount = Property::where('agent_id', $agent->id)->count();
        $requestCount = PropRequest::whereHas('property', fn ($q) => $q->where('agent_id', $agent->id))->count();
        $homeTypeCount = HomeType::count();

        return view('agent.dashboard', compact('propertyCount', 'requestCount', 'homeTypeCount'));
    }
}
