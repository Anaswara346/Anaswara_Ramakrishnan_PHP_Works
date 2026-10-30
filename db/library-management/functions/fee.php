<?php

class FeeManager
{
    private PDO $pdo;

    public function __construct(PDO $connection)
    {
        $this->pdo = $connection;
    }

    public function viewFees(): void
{
    $sql = "
        SELECT
            f.fee_id,
            u.user_name,
            b.title AS book_name,
            br.copy_id,
            f.fee_type,
            f.amount,
            f.fee_date,
            f.paid,
            f.payment_date
        FROM fee f
        JOIN library_user u
            ON f.user_id = u.user_id
        JOIN borrowing br
            ON f.borrowing_id = br.borrowing_id
        JOIN book b
            ON br.item_id = b.item_id
        ORDER BY f.fee_date DESC
    ";

    $stmt = $this->pdo->query($sql);
    $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\n======== FINE RECORDS & SUMMARY ==========\n";

    if (empty($fees)) {
        echo "No fee records found.\n";
    } else {
        foreach ($fees as $fee) {
            echo "Fine ID: " . $fee['fee_id'] . "\n";
            echo "User: " . $fee['user_name'] . "\n";
            echo "Book: " . $fee['book_name'] . "\n";
            echo "Copy ID: " . $fee['copy_id'] . "\n";
            echo "Type: " . $fee['fee_type'] . "\n";
            echo "Amount: Rs. " . $fee['amount'] . "\n";
            echo "Fine Recorded: " . $fee['fee_date'] . "\n";
            echo "Status: "
                . ($fee['paid'] ? 'Paid' : 'Unpaid') . "\n";
            echo "Payment Date: "
                . ($fee['payment_date']
                    ?? ($fee['paid'] ? 'Not recorded' : 'Not paid')) . "\n";

            echo "-------------------------------------------------------\n";
        }
    }

    $sql = "
        SELECT
            COALESCE(SUM(
                CASE WHEN paid = 1 THEN amount ELSE 0 END
            ), 0) AS total_collected,
            COALESCE(SUM(
                CASE WHEN paid = 0 THEN amount ELSE 0 END
            ), 0) AS total_outstanding,
            COALESCE(SUM(amount), 0) AS total_recorded
        FROM fee
    ";

    $stmt = $this->pdo->query($sql);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "\n========== FINE REVENUE SUMMARY ========\n";
    echo "Total Fines Recorded: Rs. "
        . $summary['total_recorded'] . "\n";
    echo "Revenue Collected: Rs. "
        . $summary['total_collected'] . "\n";
    echo "Outstanding Fines: Rs. "
        . $summary['total_outstanding'] . "\n";

    $sql = "
        SELECT
            b.item_id,
            b.title AS book_name,
            COALESCE(SUM(
                CASE WHEN f.paid = 1 THEN f.amount ELSE 0 END
            ), 0) AS revenue_collected,
            COALESCE(SUM(
                CASE WHEN f.paid = 0 THEN f.amount ELSE 0 END
            ), 0) AS outstanding_amount
        FROM book b
        JOIN borrowing br
            ON b.item_id = br.item_id
        JOIN fee f
            ON br.borrowing_id = f.borrowing_id
        GROUP BY b.item_id, b.title
        ORDER BY revenue_collected DESC
    ";

    $stmt = $this->pdo->query($sql);
    $bookRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\n========== REVENUE BY BOOK ===-======\n";

    if (empty($bookRevenue)) {
        echo "No book-related fee records found.\n";
        return;
    }

    foreach ($bookRevenue as $book) {
        echo "\nBook: " . $book['book_name'] . "\n";
        echo "Revenue Collected: Rs. "
            . $book['revenue_collected'] . "\n";
        echo "Payment to be collected: Rs. "
            . $book['outstanding_amount'] . "\n";
    }
}
    public function markFeeAsPaid(): void
    {
    echo "\n--- Mark Fine as Paid ---\n";

    $feeId = (int) getInput("Enter Fine ID: ");

    try {
        $stmt = $this->pdo->prepare(
            "SELECT fee_id, amount, paid
             FROM fee
             WHERE fee_id = :fee_id"
        );

        $stmt->execute([
            'fee_id' => $feeId
        ]);

        $fee = $stmt->fetch();

        if (!$fee) {
            echo "Fine record not found.\n";
            return;
            }

        if ($fee['paid']) {
            echo "This fine has already been paid.\n";
            return;
        }

        $update = $this->pdo->prepare(
            "UPDATE fee
             SET paid = TRUE,
                 payment_date = CURRENT_TIMESTAMP
             WHERE fee_id = :fee_id
               AND paid = FALSE"
        );

        $update->execute([
            'fee_id' => $feeId
        ]);

        if ($update->rowCount() !== 1) {
            echo "Fine could not be marked as paid. Please check its current status.\n";
            return;
        }
        $paymentDateStmt = $this->pdo->prepare(
            "SELECT payment_date
             FROM fee
             WHERE fee_id = :fee_id"
        );
        $paymentDateStmt->execute(['fee_id' => $feeId]);
        $paymentDate = $paymentDateStmt->fetchColumn();
    
        echo "Fine marked as paid successfully!\n";
        echo "Amount paid: Rs. "
            . number_format((float) $fee['amount'], 2) . "\n";
        echo "Payment date: " . $paymentDate . "\n";
    } catch (PDOException $e) {
        echo "Database operation failed: "
            . $e->getMessage() . "\n";
    }
}
}
