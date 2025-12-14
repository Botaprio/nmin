<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoardController extends Controller
{
    public function index()
    {
        $boards = auth()->user()->boards()
            ->with('columns.cards')
            ->latest()
            ->get();

        return Inertia::render('Boards/Index', [
            'boards' => $boards,
        ]);
    }

    public function create()
    {
        return Inertia::render('Boards/Create');
    }

    public function show(Board $board)
    {
        $board->load([
            'columns' => function($query) {
                $query->orderBy('position');
            },
            'columns.cards' => function($query) {
                $query->orderBy('position');
            },
            'columns.cards.scene',
            'columns.cards.assignedUsers',
            'columns.cards.tags',
            'columns.cards.comments.user',
            'columns.cards.googleDriveFiles',
            'columns.cards.youtubeVideo',
            'scenes' => function($query) {
                $query->orderBy('position');
            },
            'scenes.cards',
            'tags',
        ]);

        // Calculate completion percentage
        $completionPercentage = $board->getCompletionPercentage();

        return Inertia::render('Boards/Show', [
            'board' => $board,
            'completionPercentage' => $completionPercentage,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_template' => 'boolean',
        ]);

        $board = auth()->user()->boards()->create($validated);

        // Create default AI video production columns
        $defaultColumns = [
            ['name' => 'Prompts', 'color' => '#8B5CF6', 'position' => 0],
            ['name' => 'Edición', 'color' => '#3B82F6', 'position' => 1],
            ['name' => 'Postproducción', 'color' => '#F59E0B', 'position' => 2],
            ['name' => 'OK', 'color' => '#22C55E', 'position' => 3],
        ];

        foreach ($defaultColumns as $columnData) {
            $board->columns()->create($columnData);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $board->id,
            'action' => 'created',
            'description' => auth()->user()->name . ' created board: ' . $board->name,
        ]);

        return redirect()->route('boards.show', $board)->with('success', 'Board created successfully!');
    }

    public function edit(Board $board)
    {
        return Inertia::render('Boards/Edit', [
            'board' => $board,
        ]);
    }

    public function update(Request $request, Board $board)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        $board->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $board->id,
            'action' => 'updated',
            'description' => auth()->user()->name . ' updated board: ' . $board->name,
        ]);

        return back()->with('success', 'Board updated successfully!');
    }

    public function destroy(Board $board)
    {
        $boardName = $board->name;
        $board->delete();

        return redirect()->route('boards.index')->with('success', "Board '{$boardName}' deleted successfully!");
    }
}
