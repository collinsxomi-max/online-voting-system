<?php
define('ALLOW_DB_FAILURE', true);
include 'db.php';
session_start();

if (!isset($_SESSION['student_reg_no'])) {
    header('Location: ../frontend/login.php');
    exit;
}

if (!db_is_available()) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => db_error_message()];
    header('Location: ../frontend/vote.php');
    exit;
}

$studentRegNo = $_SESSION['student_reg_no'];
$votes = $_POST['candidate_id'] ?? [];
$votesCollection = $conn->selectCollection('votes');
$integrityCollection = $conn->selectCollection('integrity');
$submittedVote = false;

foreach ($votes as $positionId => $candidateId) {
    if (!preg_match('/^[a-f\d]{24}$/i', (string) $positionId) || !preg_match('/^[a-f\d]{24}$/i', (string) $candidateId)) {
        continue;
    }

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
        $submittedVote = true;

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

if ($submittedVote) {
    log_action($conn, 'Vote cast', $studentRegNo);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Your vote has been recorded.'];
} else {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'No valid votes were submitted.'];
}

header('Location: ../frontend/dashboard.php');
exit;
?>
