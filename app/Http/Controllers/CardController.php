<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Column;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer',
            'video_idea' => 'nullable|string',
            'script_notes' => 'nullable|string',
            'animation_prompts' => 'nullable|string',
            'music_notes' => 'nullable|string',
        ]);

        $column = Column::findOrFail($validated['column_id']);
        $validated['board_id'] = $column->board_id;
        $validated['created_by'] = auth()->id();
        $validated['position'] = $column->cards()->max('position') + 1;

        $card = Card::create($validated);

        // Assign creator to card
        $card->assignments()->create([
            'user_id' => auth()->id(),
            'role' => 'owner',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'card_id' => $card->id,
            'board_id' => $card->board_id,
            'action' => 'created',
            'description' => auth()->user()->name . ' created card: ' . $card->title,
        ]);

        return back()->with('success', 'Card created successfully!');
    }

    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer',
            'video_idea' => 'nullable|string',
            'script_notes' => 'nullable|string',
            'animation_prompts' => 'nullable|string',
            'music_notes' => 'nullable|string',
            'cover_image' => 'nullable|string',
        ]);

        $card->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'card_id' => $card->id,
            'board_id' => $card->board_id,
            'action' => 'updated',
            'description' => auth()->user()->name . ' updated card: ' . $card->title,
        ]);

        return back()->with('success', 'Card updated successfully!');
    }

    public function move(Request $request, Card $card)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'position' => 'required|integer|min:0',
        ]);

        $oldColumn = $card->column;
        $newColumn = Column::findOrFail($validated['column_id']);

        $card->update([
            'column_id' => $validated['column_id'],
            'position' => $validated['position'],
        ]);

        // Reorder cards in both columns
        $this->reorderCards($oldColumn);
        if ($oldColumn->id !== $newColumn->id) {
            $this->reorderCards($newColumn);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'card_id' => $card->id,
            'board_id' => $card->board_id,
            'action' => 'moved',
            'description' => auth()->user()->name . ' moved card from ' . $oldColumn->name . ' to ' . $newColumn->name,
            'metadata' => [
                'from_column' => $oldColumn->name,
                'to_column' => $newColumn->name,
            ],
        ]);

        return back();
    }

    public function destroy(Card $card)
    {
        $cardTitle = $card->title;
        $boardId = $card->board_id;

        $card->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $boardId,
            'action' => 'deleted',
            'description' => auth()->user()->name . ' deleted card: ' . $cardTitle,
        ]);

        return back()->with('success', 'Card deleted successfully!');
    }

    public function assignUser(Request $request, Card $card)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'in:owner,editor,reviewer,viewer',
        ]);

        $card->assignments()->updateOrCreate(
            ['user_id' => $validated['user_id']],
            ['role' => $validated['role'] ?? 'editor']
        );

        return back()->with('success', 'User assigned successfully!');
    }

    private function reorderCards(Column $column)
    {
        $cards = $column->cards()->orderBy('position')->get();
        foreach ($cards as $index => $card) {
            $card->update(['position' => $index]);
        }
    }
}
