<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: ../frontend/admin_login.php');
    exit;
}

$type = $_GET['type'] ?? 'full';
$type = strtolower(trim($type));

if ($type !== 'winners') {
    $type = 'full';
}

$pipeline = [
    [
        '$lookup' => [
            'from' => 'departments',
            'localField' => 'department_id',
            'foreignField' => 'department_id',
            'as' => 'department'
        ]
    ],
    [
        '$lookup' => [
            'from' => 'positions',
            'localField' => 'position_id',
            'foreignField' => 'position_id',
            'as' => 'position'
        ]
    ],
    [
        '$lookup' => [
            'from' => 'votes',
            'localField' => 'candidate_id',
            'foreignField' => 'candidate_id',
            'as' => 'votes'
        ]
    ],
    [
        '$addFields' => [
            'department_name' => ['$arrayElemAt' => ['$department.name', 0]],
            'position_name' => ['$arrayElemAt' => ['$position.position_name', 0]],
            'total_votes' => ['$size' => '$votes']
        ]
    ],
    [
        '$sort' => [
            'department_name' => 1,
            'position_name' => 1,
            'total_votes' => -1,
            'name' => 1
        ]
    ]
];

$cursor = db_collection('candidates')->aggregate($pipeline);

if ($type === 'winners') {
    $winners = [];
    $topScores = [];
    foreach ($cursor as $doc) {
        $row = db_to_array($doc);
        $key = $row['department_name'] . '||' . $row['position_name'];
        $votes = (int)$row['total_votes'];
        if (!isset($topScores[$key]) || $votes > $topScores[$key]) {
            $topScores[$key] = $votes;
            $winners[$key] = [$row];
        } elseif ($votes === $topScores[$key]) {
            $winners[$key][] = $row;
        }
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="election_winners.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Department', 'Position', 'Candidate', 'Votes']);

    foreach ($winners as $rows) {
        foreach ($rows as $row) {
            fputcsv($output, [$row['department_name'], $row['position_name'], $row['name'], $row['total_votes']]);
        }
    }

    fclose($output);
    exit;
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="election_results.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Department', 'Position', 'Candidate', 'Votes']);

foreach ($cursor as $doc) {
    $row = db_to_array($doc);
    fputcsv($output, [$row['department_name'], $row['position_name'], $row['name'], $row['total_votes']]);
}

fclose($output);
exit;
