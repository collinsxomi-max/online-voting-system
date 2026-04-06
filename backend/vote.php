<?php
require_once __DIR__ . '/../includes/security.php';
define('ALLOW_DB_FAILURE', true);
include 'db.php';

require_student_session('../frontend/login.php', 'Please log in to cast your vote.');

if (!db_is_available()) {
    set_flash_message('error', db_error_message());
    redirect_to('../frontend/vote.php');
}

require_valid_csrf('../frontend/vote.php');

$studentRegNo = $_SESSION['student_reg_no'];
$votes = $_POST['candidate_id'] ?? [];
$votesCollection = $conn->selectCollection('votes');
$integrityCollection = $conn->selectCollection('integrity');
$candidatesCollection = $conn->selectCollection('candidates');
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

    $positionObjectId = new \MongoDB\BSON\ObjectId($positionId);
    $candidateObjectId = new \MongoDB\BSON\ObjectId($candidateId);
    $candidate = $candidatesCollection->findOne([
        '_id' => $candidateObjectId,
        'position_id' => $positionObjectId
    ]);

    if (!$candidate) {
        continue;
    }

    try {
        $result = $votesCollection->insertOne([
            'student_reg_no' => $studentRegNo,
            'candidate_id' => $candidateObjectId,
            'position_id' => $positionObjectId,
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
    set_flash_message('success', 'Your vote has been recorded.');
} else {
    set_flash_message('error', 'No valid votes were submitted.');
}

redirect_to('../frontend/dashboard.php');
?>
