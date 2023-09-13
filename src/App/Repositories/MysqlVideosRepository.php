<?php

namespace App\Repositories;

class MysqlVideosRepository implements VideosRepository
{
    protected $dbh;

    public function __construct(\Mysqli $dbh)
    {
        $this->dbh = $dbh;
    }

    public function create($skill_id, $youtube_id)
    {
        $sql = 'INSERT INTO videos (
              video_id,
              skill_id,
              youtube_id,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, now(), now())';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bind_param('is', $skill_id, $youtube_id);
        $stmt->execute();
        $stmt->close();

        return $this->dbh->insert_id;
    }
}
