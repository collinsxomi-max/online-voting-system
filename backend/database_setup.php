<?php
function createCollectionIfMissing($conn, string $name): void
{
    $collections = [];
    foreach ($conn->listCollections([], ['nameOnly' => true]) as $collection) {
        $collections[] = $collection->getName();
    }

    if (!in_array($name, $collections, true)) {
        $conn->createCollection($name);
    }
}

function initializeDatabase($conn): array
{
    $createdCollections = [];
    $seededPositions = [];

    $requiredCollections = [
        'students',
        'positions',
        'candidates',
        'votes',
        'audit_log',
        'integrity',
    ];

    $existingCollections = [];
    foreach ($conn->listCollections([], ['nameOnly' => true]) as $collection) {
        $existingCollections[] = $collection->getName();
    }

    foreach ($requiredCollections as $collectionName) {
        if (!in_array($collectionName, $existingCollections, true)) {
            $conn->createCollection($collectionName);
            $createdCollections[] = $collectionName;
        }
    }

    $conn->selectCollection('students')->createIndex(['reg_no' => 1], ['unique' => true, 'name' => 'students_reg_no_unique']);
    $conn->selectCollection('students')->createIndex(['email' => 1], ['name' => 'students_email']);
    $conn->selectCollection('positions')->createIndex(['position_name' => 1], ['unique' => true, 'name' => 'positions_name_unique']);
    $conn->selectCollection('candidates')->createIndex(['position_id' => 1], ['name' => 'candidates_position_id']);
    $conn->selectCollection('votes')->createIndex(
        ['student_reg_no' => 1, 'position_id' => 1],
        ['unique' => true, 'name' => 'votes_student_position_unique']
    );
    $conn->selectCollection('votes')->createIndex(['candidate_id' => 1], ['name' => 'votes_candidate_id']);
    $conn->selectCollection('audit_log')->createIndex(['timestamp' => -1], ['name' => 'audit_log_timestamp_desc']);
    $conn->selectCollection('integrity')->createIndex(['vote_id' => 1], ['unique' => true, 'name' => 'integrity_vote_id_unique']);

    $positionsCollection = $conn->selectCollection('positions');
    if ($positionsCollection->countDocuments() === 0) {
        $defaultPositions = [
            ['position_name' => 'President', 'description' => 'Leads the student body and represents voters.'],
            ['position_name' => 'Vice President', 'description' => 'Supports the president and coordinates activities.'],
            ['position_name' => 'Secretary', 'description' => 'Maintains records and election communication.'],
            ['position_name' => 'Treasurer', 'description' => 'Oversees financial accountability for the union.'],
        ];

        $timestamp = new MongoDB\BSON\UTCDateTime(time() * 1000);
        foreach ($defaultPositions as $position) {
            $positionsCollection->insertOne([
                'position_name' => $position['position_name'],
                'description' => $position['description'],
                'created_at' => $timestamp,
            ]);
            $seededPositions[] = $position['position_name'];
        }
    }

    return [
        'created_collections' => $createdCollections,
        'seeded_positions' => $seededPositions,
    ];
}
