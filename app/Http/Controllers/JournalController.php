<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    public function getData(Request $request)
    {
        $query = Journal::query();
        $query->with('tags');

        $query->where('user_id', Auth::user()->id);

        $search = $request->input('search', '');

        if ($search) {
            $query->where('title', 'LIKE', "%$search%")->orWhere('content',  'LIKE', "%$search%")->orWhere('created_at',  'LIKE', "%$search%");
        }

        $journals = $query->get();
        return response()->json($journals);
    }


    public function show(Journal $journal)
    {
        $journal->load('tags');
        return response()->json($journal);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'required|string',
            'cover' => 'nullable|image|max:2048',
        ]);

        $journal = Auth::user()->journals()->create($request->only('title', 'content'));

        $tags = explode(',', $request->tags);
        $tagIds = [];

        foreach ($tags as $item) {
            $tag = Tag::firstOrCreate(['name' => trim($item)]);
            $tagIds[] = $tag->id;
        }

        $journal->tags()->syncWithoutDetaching($tagIds);

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('journals', $filename, 'public');

            $journal->update([
                'cover_url' => $path
            ]);
        }

        return response()->json($journal);
    }

    public function update(Request $request, Journal $journal)
    {
        $journal->update($request->only('title', 'content'));

        $tags = explode(',', $request->tags);
        $tagIds = [];

        foreach ($tags as $item) {
            $tag = Tag::firstOrCreate(['name' => trim($item)]);
            $tagIds[] = $tag->id;
        }

        $journal->tags()->sync($tagIds);

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('journals', $filename, 'public');

            $journal->update([
                'cover_url' => $path
            ]);
        }

        return response()->json($journal);
    }

    public function destroy(Journal $journal)
    {
        $journal->delete();
        return response()->json([
            'message' => 'Journal deleted successfully',
        ]);
    }

    public function index()
    {
        return view('pages.journals.index');
    }
}
