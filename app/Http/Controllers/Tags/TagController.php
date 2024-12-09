<?php

namespace App\Http\Controllers\Tags;

use Spatie\Tags\Tag;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\SyncTagsRequest;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function fetchall() {
        return Tag::all();
    }

    public function synctags(SyncTagsRequest $request)
    {
        $request->model->syncTags($request->relevanttags);

        return New $request->model->load(['tags']);
    }

    public function addtag(SyncTagsRequest $request)
    {
        $request->model->attachTag($request->relevanttags[0]);

        return $request->model->load(['tags']);
    }

    public function removetag(SyncTagsRequest $request)
    {
        $tag = Tag::where('id', $request->relevanttags[0])->first();
        if ($tag) {
            $request->model->detachTag($tag->name);
            if ( DB::table( config('tags.taggable.table_name') )->where('tag_id', $tag->id)->doesntExist() ) {
                $tag->delete();
            }
        }

        return $request->model->load(['tags']);
    }
}
