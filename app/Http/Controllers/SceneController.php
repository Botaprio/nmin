<?php

namespace App\Http\Controllers;

use App\Models\Scene;
use App\Models\Board;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SceneController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $board = Board::findOrFail($validated['board_id']);
        
        // Get next position
        $maxPosition = $board->scenes()->max('position') ?? -1;
        $validated['position'] = $maxPosition + 1;

        $scene = Scene::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $board->id,
            'action' => 'scene_created',
            'description' => auth()->user()->name . ' creó la escena: ' . $scene->name,
        ]);

        return back()->with('success', 'Escena creada correctamente');
    }

    public function update(Request $request, Scene $scene)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $scene->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $scene->board_id,
            'action' => 'scene_updated',
            'description' => auth()->user()->name . ' actualizó la escena: ' . $scene->name,
        ]);

        return back()->with('success', 'Escena actualizada');
    }

    public function destroy(Scene $scene)
    {
        $boardId = $scene->board_id;
        $sceneName = $scene->name;

        // Set scene_id to null for all cards in this scene
        $scene->cards()->update(['scene_id' => null]);

        $scene->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $boardId,
            'action' => 'scene_deleted',
            'description' => auth()->user()->name . ' eliminó la escena: ' . $sceneName,
        ]);

        return back()->with('success', 'Escena eliminada');
    }
}
