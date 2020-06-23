<?php
namespace FastQ\Adapters\SqlBase;

use FastQ\Job;
use FastQ\Adapters\Interfaces\{ Pull, Push, Output };
use PDO;

/**
 * Make this works in all databases(DBAL? or PHINX?)
 */
class SqlExecutor implements Pull, Push, Output
{
    protected $pdo;

    protected $filters;

    protected $preparedProps = [];

    protected $baseQuery;

    public function __construct(PDO $pdo)
    {    
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getJobs(string $channel = null): array
    {
        $this->filters = 'WHERE `after` <= ' . strtotime('now');
        $this->filters .= ' AND `tries` = 0';
        $this->filters .= ' AND `status` = 0';

        if ($channel != null) {
            $this->filters .=  ' AND `channel` = ?';
            $this->preparedProps = [$channel];
        }

        return $this->execSearch();
    }

    public function getFailedJobs(string $channel = null): array
    {
        $this->filters = 'WHERE `after` <= ' . strtotime('now');
        $this->filters .= ' AND `status` = 0';
        $this->filters .= ' AND `tries` > 0';

        if ($channel != null) {
            $this->filters .=  ' AND `channel` = ?';
            $this->preparedProps = [$channel];
        }

        return $this->execSearch();
    }

    public function execSearch(): array
    {
        $baseQuery = "SELECT * FROM fastq_jobs $this->filters";

        $stmt = $this->pdo->prepare($baseQuery);

        $stmt->execute($this->preparedProps);

        $this->preparedProps = [];
        $this->filters = '';

        return $stmt->fetchAll();
    }

    public function replaceJob(Job $job): bool
    {
        $set = [];

        foreach ($job->getArray() as $prop => $value) {
            if ($prop === 'id') continue;
            $set[] = "`$prop` = ?";
        }

        $setString = implode(', ', $set);

        $query = "UPDATE fastq_jobs SET $setString WHERE id=?";

        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([
            $job->getChannel(),
            $job->getAction(),      
            $job->getData(),        
            $job->getStatus(),      
            $job->getTries(),       
            $job->getAfter(),
            $job->getId()      
        ]);
    }

    public function completeJob(Job $job): bool
    {
        $query = "DELETE FROM fastq_jobs WHERE id=?";

        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([$job->id]);
    }

    public function addJob(Job $job): bool
    {
        $query = "INSERT INTO fastq_jobs (`channel`, `action`, `data`, `after`) VALUES (?, ?, ?, ?)";

        $jobArray = $job->getArray();

        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([
            $jobArray['channel'],
            $jobArray['action'],
            $jobArray['data'],
            $jobArray['after']
        ]);
    }
}