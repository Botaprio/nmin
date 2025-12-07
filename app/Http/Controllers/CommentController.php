<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Card;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required|exists:cards,id',
            'content' => 'required|string',
        ]);

        $card = Card::findOrFail($validated['card_id']);

        $comment = Comment::create([
            'card_id' => $card->id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $card->board_id,
            'card_id' => $card->id,
            'action' => 'comment_added',
            'description' => auth()->user()->name . ' agregó un comentario',
        ]);

        return back()->with('success', 'Comentario agregado');
    }

    public function update(Request $request, Comment $comment)
    {
        // Authorization check
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'No autorizado para editar este comentario');
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update($validated);

        return back()->with('success', 'Comentario actualizado');
    }

    public function destroy(Comment $comment)
    {
        // Authorization check
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'No autorizado para eliminar este comentario');
        }

        $cardId = $comment->card_id;
        $boardId = $comment->card->board_id;

        $comment->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'board_id' => $boardId,
            'card_id' => $cardId,
            'action' => 'comment_deleted',
            'description' => auth()->user()->name . ' eliminó un comentario',
        ]);

        return back()->with('success', 'Comentario eliminado');
    }
}
