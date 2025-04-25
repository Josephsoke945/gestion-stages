<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StructureController extends Controller
{
    public function index()
    {
        $structures = Structure::withCount('stagiaires')
            ->where('active', true)
            ->latest()
            ->paginate(10);

        return Inertia::render('Agent/Structures/Index', [
            'structures' => $structures
        ]);
    }

    public function show(Structure $structure)
    {
        $structure->load(['stagiaires.user']);
        $structure->loadCount('stagiaires');

        return Inertia::render('Agent/Structures/Show', [
            'structure' => $structure
        ]);
    }
} 