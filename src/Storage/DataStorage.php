<?php

namespace App\Storage;

use App\Model;

/**
 * Architecture of this class is against the principles of SOLID
 * This class depends on PDO object, this is not good solution. It should depends on abstraction.
 * Why not to use some ORM? :)
 */

class DataStorage
{
    /**
     * @var \PDO
     */
    public $pdo;

	//Need to add mysql connection data as params
    public function __construct()
    {
        $this->pdo = new \PDO('mysql:dbname=task_tracker;host=127.0.0.1', 'user');
    }

	//Need to declare return statement
    /**
     * @param int $projectId
     * @throws Model\NotFoundException
     */
    public function getProjectById($projectId)
    {
		//Need to use pdo->prepare method for all queries
        $stmt = $this->pdo->query('SELECT * FROM project WHERE id = ' . (int) $projectId);

        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return new Model\Project($row);
        }

        throw new Model\NotFoundException();
    }


	//Need to declare return statement
    /**
     * @param int $project_id
     * @param int $limit
     * @param int $offset
     */
	//Need to add type to all params
    public function getTasksByProjectId(int $project_id, $limit, $offset)
    {
		//need to use pdo->prepare method
        $stmt = $this->pdo->query("SELECT * FROM task WHERE project_id = $project_id LIMIT ?, ?");
        $stmt->execute([$limit, $offset]);
        $tasks = [];
        foreach ($stmt->fetchAll() as $row) {
            $tasks[] = new Model\Task($row);
        }

        return $tasks;
    }

    /**
     * @param array $data
     * @param int $projectId
     * @return Model\Task
     */

	//Need to add type to all params
    public function createTask(array $data, $projectId)
    {
        $data['project_id'] = $projectId;

        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(function ($v) {
            return is_string($v) ? '"' . $v . '"' : $v;
        }, $data));

		//Need to use pdo->prepare method
        $this->pdo->query("INSERT INTO task ($fields) VALUES ($values)");
		//To get id of inserted row you can use pdo->lastInsertId() method
        $data['id'] = $this->pdo->query('SELECT MAX(id) FROM task')->fetchColumn();

        return new Model\Task($data);
    }
}
