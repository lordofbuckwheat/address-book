<?php

class RecordManager
{

    /** @var PDO $pdo */
    private $pdo;
    private $lastError;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->query('CREATE TABLE IF NOT EXISTS `records` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `created` datetime NOT NULL,
          `name` varchar(255) NOT NULL,
          `phone` varchar(255) NOT NULL,
          `email` varchar(255) NOT NULL,
          PRIMARY KEY (`id`),
          FULLTEXT (name)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }

    private function validate(array $record): bool
    {
        if (empty($record['name'])) {
            $this->lastError = 'Invalid name';
            return false;
        }
        if (empty($record['phone']) || !preg_match("/^[\d\+\-]*$/", $record['phone'])) {
            $this->lastError = 'Invalid phone';
            return false;
        }
        if (empty($record['email']) || !filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
            $this->lastError = 'Invalid email';
            return false;
        }
        return true;
    }

    public function insertMany(array $records)
    {
        $records = array_filter($records, function ($record) {
            return $this->validate($record);
        });
        $inserts = [];
        $data = [];
        foreach ($records as $key => $record) {
            $inserts[] = '(NOW(),?,?,?)';
            $data[] = $record['name'];
            $data[] = $record['phone'];
            $data[] = $record['email'];
        }
        $stmt = $this->pdo->prepare(sprintf('INSERT INTO records (created, name, phone, email) VALUES%s;', implode(',', $inserts)));
        $stmt->execute($data);
    }

    public function insert(array $record): int
    {
        if (!$this->validate($record)) {
            throw new Exception($this->lastError);
        }
        $stmt = $this->pdo->prepare('INSERT INTO records (created, name, phone, email) VALUES(NOW(),:name,:phone,:email);');
        $stmt->execute($record);
        return $this->pdo->lastInsertId();
    }

    public function update(int $id, array $record)
    {
        if (!$this->validate($record)) {
            throw new Exception($this->lastError);
        }
        $stmt = $this->pdo->prepare('UPDATE records SET name=:name, phone=:phone, email=:email WHERE id=:id;');
        $record['id'] = $id;
        $stmt->execute($record);
        if (!$stmt->rowCount()) {
            throw new Exception('Record not updated');
        }
    }

    public function delete(int $id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM records WHERE id=?;');
        $stmt->execute([$id]);
        if (!$stmt->rowCount()) {
            throw new Exception('Record not found');
        }
    }

    public function get(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM records ORDER BY created DESC;');
        return $stmt->fetchAll();
    }

    public function search(string $query): array
    {
        $query = preg_replace('/[^\w\s\d]/u', '', $query);
        $stmt = $this->pdo->prepare('SELECT id, name, phone, email, MATCH (name) AGAINST (:query IN BOOLEAN MODE) AS relevance 
            FROM records
            WHERE MATCH (name) AGAINST (:query IN BOOLEAN MODE)
            ORDER BY relevance DESC, created DESC');
        $stmt->execute(['query' => $query.'*']);
        return array_map(function($row) {
            return [
                'id' => $row['id'],
                'name' => $row['name'],
                'phone' => $row['phone'],
                'email' => $row['email']
            ];
        }, $stmt->fetchAll());
    }

}