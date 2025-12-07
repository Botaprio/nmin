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
            'columns.cards.assignedUsers',
            'columns.cards.tags',
            'columns.cards.comments.user',
            'columns.cards.googleDriveFiles',
            'columns.cards.youtubeVideo',
            'tags',
        ]);

        return Inertia::render('Boards/Show', [
            'board' => $board,
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
            ['name' => 'Ideas', 'color' => '#F59E0B', 'position' => 0],
            ['name' => 'Script Writing', 'color' => '#3B82F6', 'position' => 1],
            ['name' => 'Pre-production', 'color' => '#8B5CF6', 'position' => 2],
            ['name' => 'Animating', 'color' => '#EC4899', 'position' => 3],
            ['name' => 'Editing', 'color' => '#10B981', 'position' => 4],
            ['name' => 'Review/Approval', 'color' => '#F97316', 'position' => 5],
            ['name' => 'Publishing', 'color' => '#06B6D4', 'position' => 6],
            ['name' => 'Published', 'color' => '#22C55E', 'position' => 7],
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
