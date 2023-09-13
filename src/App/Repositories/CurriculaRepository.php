<?php

namespace App\Repositories;

interface curriculaRepository
{
    public function find($curriculumId);

    public function findOrFail($curriculumId);

    public function findByName($name);

    public function findByNameOrFail($name);

    public function findBySlug($slug);

    public function findBySlugOrFail($slug);

    public function all();

    public function findTopics($curriculumId, $userId);

    public function findTopicsOrFail($curriculumId, $userId);

    public function create($name, $slug, $displayOrder);

    public function updateBySlug($name, $slug, $displayOrder);
}
