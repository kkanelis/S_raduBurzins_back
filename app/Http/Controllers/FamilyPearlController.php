<?php

namespace App\Http\Controllers;

use App\Models\FamilyPearl;
use App\Models\FamilyPearlMedia;
use App\Models\FamilyPearlReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FamilyPearlController extends Controller
{
    public function options(): JsonResponse
    {
        return response()->json([
            'categories' => \App\Models\FamilyPearlCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->integer('category_id');

        $pearls = FamilyPearl::query()
            ->with(['category', 'media', 'user', 'reactions'])
            ->withCount('media')
            ->where('status', 'published')
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                    ->orWhere('text', 'like', "%{$search}%")
                    ->orWhere('context', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $pearls->getCollection()->transform(fn (FamilyPearl $pearl) => $this->normalize($pearl));

        return response()->json($pearls);
    }

    public function show(FamilyPearl $pearl): JsonResponse
    {
        abort_unless($pearl->status === 'published' && $pearl->published_at, 404);

        $pearl->load(['category', 'media', 'user', 'reactions']);

        return response()->json($this->normalize($pearl, true));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'text' => ['required', 'string', 'max:5000'],
            'context' => ['nullable', 'string', 'max:3000'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('family_pearl_categories', 'id')->where('is_active', true),
            ],
            'is_anonymous' => ['sometimes', 'boolean'],
            'media' => ['nullable', 'array', 'max:8'],
            'media.*' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm',
                'max:51200',
            ],
        ]);

        $storedPaths = [];

        try {
            $pearl = DB::transaction(function () use ($request, $validated, &$storedPaths) {
                $pearl = FamilyPearl::create([
                    'user_id' => $request->user()->getKey(),
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'text' => $validated['text'],
                    'context' => $validated['context'] ?? null,
                    'is_anonymous' => $request->boolean('is_anonymous'),
                    'status' => 'published',
                    'published_at' => now(),
                ]);

                foreach ($request->file('media', []) as $index => $file) {
                    $mime = $file->getMimeType();
                    $type = str_starts_with($mime, 'video/') ? 'video' : 'image';
                    $path = $file->store('family-pearls/'.$pearl->id, 'private');

                    if (! $path) {
                        throw new \RuntimeException('Pērles pielikumu neizdevās saglabāt.');
                    }

                    $storedPaths[] = $path;

                    FamilyPearlMedia::create([
                        'pearl_id' => $pearl->id,
                        'type' => $type,
                        'path' => $path,
                        'disk' => 'private',
                        'mime_type' => $mime,
                        'size' => $file->getSize(),
                        'sort_order' => $index,
                    ]);
                }

                return $pearl;
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('private')->delete($path);
            }

            throw $exception;
        }

        return response()->json([
            'message' => 'Pērle veiksmīgi publicēta.',
            'pearl' => $this->normalize($pearl->fresh(['category', 'media'])),
        ], 201);
    }

    public function media(Request $request, FamilyPearlMedia $media)
    {
        $media->load('pearl');
        abort_unless($media->pearl && $media->pearl->status === 'published', 404);

        if (! Storage::disk('private')->exists($media->path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('private')->path($media->path),
            ['Content-Type' => $media->mime_type]
        );
    }

    public function react(Request $request, FamilyPearl $pearl): JsonResponse
    {
        abort_unless($pearl->status === 'published', 404);

        $validated = $request->validate([
            'emoji' => ['required', 'string', 'in:😂,❤️,🤯,🏆'],
        ]);

        $reaction = FamilyPearlReaction::query()->updateOrCreate(
            [
                'pearl_id' => $pearl->id,
                'user_id' => $request->user()->getKey(),
            ],
            ['emoji' => $validated['emoji']],
        );

        $pearl->load(['reactions']);

        return response()->json([
            'message' => 'Reakcija saglabāta.',
            'reactions' => $this->reactionCounts($pearl, $request->user()->getKey()),
        ]);
    }

    public function removeReaction(Request $request, FamilyPearl $pearl): JsonResponse
    {
        abort_unless($pearl->status === 'published', 404);

        FamilyPearlReaction::query()
            ->where('pearl_id', $pearl->id)
            ->where('user_id', $request->user()->getKey())
            ->delete();

        $pearl->load(['reactions']);

        return response()->json([
            'message' => 'Reakcija noņemta.',
            'reactions' => $this->reactionCounts($pearl, $request->user()->getKey()),
        ]);
    }

    private function reactionCounts(FamilyPearl $pearl, int $currentUserId): array
    {
        $counts = ['😂' => 0, '❤️' => 0, '🤯' => 0, '🏆' => 0];
        $active = null;

        foreach ($pearl->reactions as $reaction) {
            if (array_key_exists($reaction->emoji, $counts)) {
                $counts[$reaction->emoji]++;
            }
            if ((int) $reaction->user_id === $currentUserId) {
                $active = $reaction->emoji;
            }
        }

        return [
            'counts' => $counts,
            'active' => $active,
        ];
    }

    private function normalize(FamilyPearl $pearl, bool $includeAuthor = false): array
    {
        $author = null;
        if (! $pearl->is_anonymous) {
            $author = [
                'id' => $pearl->user_id,
                'name' => trim(($pearl->user->first_name ?? '').' '.($pearl->user->last_name ?? '')),
            ];
        }

        return [
            'id' => $pearl->id,
            'title' => $pearl->title,
            'text' => $pearl->text,
            'context' => $pearl->context,
            'is_anonymous' => (bool) $pearl->is_anonymous,
            'status' => $pearl->status,
            'author' => $includeAuthor ? $author : $author,
            'category' => $pearl->category ? [
                'id' => $pearl->category->id,
                'name' => $pearl->category->name,
                'slug' => $pearl->category->slug,
            ] : null,
            'media' => $pearl->media->map(fn (FamilyPearlMedia $media) => [
                'id' => $media->id,
                'type' => $media->type,
                'mime_type' => $media->mime_type,
                'url' => url('/api/family-pearls/media/'.$media->id.'/file'),
            ])->all(),
            'published_at' => $pearl->published_at?->toDateTimeString(),
            'created_at' => $pearl->created_at?->toDateTimeString(),
            'reactions' => $includeAuthor
                ? $this->reactionCounts($pearl, (int) request()->user()->getKey())
                : $this->reactionCounts($pearl, (int) request()->user()->getKey()),
        ];
    }
}
