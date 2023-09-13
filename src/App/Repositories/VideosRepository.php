<?php

namespace App\Repositories;

interface VideosRepository
{
    public function create($skill_id, $youtube_id);
}
