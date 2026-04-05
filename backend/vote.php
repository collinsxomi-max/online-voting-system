<?php
include 'db.php';
session_start();

if (!isset($_SESSION['student_reg_no'])) {
    header('Location: ../frontend/login.php');
    exit;
}

$studentRegNo = $_SESSION['student_reg_no'];
$votes = $_POST['candidate_id'] ?? [];
$votesCollection = $conn->selectCollection('votes');
$integrityCollection = $conn->selectCollection('integrity');

foreach ($votes as $positionId => $candidateId) {
    // Don't cast to int - these are MongoDB ObjectIds as strings

    $existing = $votesCollection->findOne([
        'student_reg_no' => $studentRegNo,
        'position_id' => new \MongoDB\BSON\ObjectId($positionId)
    ]);

    if ($existing) {
        continue;
    }

    try {
        $result = $votesCollection->insertOne([
            'student_reg_no' => $studentRegNo,
            'candidate_id' => new \MongoDB\BSON\ObjectId($candidateId),
            'position_id' => new \MongoDB\BSON\ObjectId($positionId),
            'vote_time' => new \MongoDB\BSON\UTCDateTime(time() * 1000)
        ]);

        $vote_id = (string) $result->getInsertedId();
        $vote_hash = hash("sha256", $studentRegNo . "-" . $positionId . "-" . $candidateId . "-" . $vote_id);

        $integrityCollection->insertOne([
            'vote_id' => $vote_id,
            'vote_hash' => $vote_hash
        ]);
    } catch (\Exception $e) {
        error_log('Vote insertion failed: ' . $e->getMessage());
    }
}

log_action($conn, 'Vote cast', $studentRegNo);
header('Location: ../frontend/dashboard.php');
exit;
?>
