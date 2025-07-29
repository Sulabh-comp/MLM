<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        $data = Region::where('id', $manager->region_id)->paginate(10);
        return view('manager.regions.index', compact('data'));
    }

    public function show(Region $region)
    {
        $manager = auth()->guard('manager')->user();
        // Check if this is manager's region
        if($region->id !== $manager->region_id) {
            abort(403, 'Unauthorized access to region data.');
        }

        return view('manager.regions.show', compact('region'));
    }
}
