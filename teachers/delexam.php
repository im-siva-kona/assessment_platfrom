<?php 
include('../config.php');

if(isset($_POST['delete_btn']))
{
    $id = $_POST['delete_id'];

    // Start a transaction to ensure all deletions are atomic
    mysqli_begin_transaction($conn);

    try {
        // Delete related rows from code_submissions
        $query = "DELETE FROM code_submissions WHERE exid='$id'";
        $query_run = mysqli_query($conn, $query);

        // Delete related rows from qstn_list
        $query = "DELETE FROM qstn_list WHERE exid='$id'";
        $query_run = mysqli_query($conn, $query);

        // Delete related rows from atmpt_list
        $query = "DELETE FROM atmpt_list WHERE exid='$id'";
        $query_run = mysqli_query($conn, $query);

        // Finally, delete the exam from exm_list
        $query = "DELETE FROM exm_list WHERE exid='$id'";
        $query_run = mysqli_query($conn, $query);

        // Commit the transaction
        mysqli_commit($conn);

        header('Location: exams.php'); 
    } catch (Exception $e) {
        // Rollback the transaction in case of any error
        mysqli_rollback($conn);

        echo "<script>alert('Your Data is NOT DELETED');</script>";      
        header('Location: exams.php'); 
    }
}
?>