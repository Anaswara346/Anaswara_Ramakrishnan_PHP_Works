
<?php

function viewFees(PDO $pdo): void
{
    echo "\n--- View All Fees ---\n";

    try {
        $sql = "SELECT
                    f.fee_id,
                    u.user_name,
                    f.borrowing_id,
                    f.fee_type,
                    f.amount,
                    f.fee_date,
                    f.paid
                FROM fee AS f
                JOIN library_user AS u
                    ON f.user_id = u.user_id
                ORDER BY f.fee_id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $fees = $stmt->fetchAll();

        if (empty($fees)) {
            echo "No fee records found.\n";
            return;
        }

        foreach ($fees as $fee) {
            echo "\n-----------------------------\n";
            echo "Fee ID       : " . $fee['fee_id'] . "\n";
            echo "User         : " . $fee['user_name'] . "\n";
            echo "Borrowing ID : " . $fee['borrowing_id'] . "\n";
            echo "Fee Type     : " . $fee['fee_type'] . "\n";
            echo "Amount       : Rs. "
                . number_format((float) $fee['amount'], 2) . "\n";
            echo "Fee Date     : " . $fee['fee_date'] . "\n";
            echo "Payment      : "
                . ($fee['paid'] ? 'Paid' : 'Unpaid') . "\n";
        }

    } catch (PDOException $e) {
        echo "Database operation failed: "
            . $e->getMessage() . "\n";
    }
}


function addLostItemFee(PDO $pdo): void
{
    echo "\n--- Add Lost Item Fee ---\n";

    $borrowingId = (int) getInput("Enter Borrowing ID: ");

    try {
        $sql = "SELECT
                    b.borrowing_id,
                    b.user_id,
                    b.return_date,
                    b.item_id,
                    u.user_name,
                    i.title,
                    i.price
                FROM borrowing AS b
                JOIN library_user AS u
                    ON b.user_id = u.user_id
                JOIN book AS i
                    ON b.item_id = i.item_id
                WHERE b.borrowing_id = :borrowing_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'borrowing_id' => $borrowingId
        ]);

        $borrowing = $stmt->fetch();

        if (!$borrowing) {
            echo "Borrowing record not found.\n";
            return;
        }

        if ($borrowing['return_date'] !== null) {
            echo "This item has already been returned. "
                . "Check the borrowing record before adding a lost-item fee.\n";
            return;
        }

        // Prevent duplicate lost-item fees
        $check = $pdo->prepare(
            "SELECT fee_id
             FROM fee
             WHERE borrowing_id = :borrowing_id
               AND fee_type = 'LOST'
             LIMIT 1"
        );

        $check->execute([
            'borrowing_id' => $borrowingId
        ]);

        if ($check->fetch()) {
            echo "A lost-item fee already exists for this borrowing.\n";
            return;
        }

        $amount = (float) $borrowing['price'];

        $insert = $pdo->prepare(
            "INSERT INTO fee
                (user_id, borrowing_id, fee_type, amount, fee_date, paid)
             VALUES
                (:user_id, :borrowing_id, 'LOST', :amount, :fee_date, FALSE)"
        );

        $insert->execute([
            'user_id' => $borrowing['user_id'],
            'borrowing_id' => $borrowingId,
            'amount' => $amount,
            'fee_date' => date('Y-m-d')
        ]);

        echo "\nLost-item fee added successfully!\n";
        echo "User   : " . $borrowing['user_name'] . "\n";
        echo "Book   : " . $borrowing['title'] . "\n";
        echo "Amount : Rs. " . number_format($amount, 2) . "\n";

    } catch (PDOException $e) {
        echo "Database operation failed: "
            . $e->getMessage() . "\n";
    }
}



function markFeeAsPaid(PDO $pdo): void
{
    echo "\n--- Mark Fee as Paid ---\n";

    $feeId = (int) getInput("Enter Fee ID: ");

    try {
        $stmt = $pdo->prepare(
            "SELECT fee_id, amount, paid
             FROM fee
             WHERE fee_id = :fee_id"
        );

        $stmt->execute([
            'fee_id' => $feeId
        ]);

        $fee = $stmt->fetch();

        if (!$fee) {
            echo "Fee record not found.\n";
            return;
        }

        if ($fee['paid']) {
            echo "This fee has already been paid.\n";
            return;
        }

        $update = $pdo->prepare(
            "UPDATE fee
             SET paid = TRUE
             WHERE fee_id = :fee_id"
        );

        $update->execute([
            'fee_id' => $feeId
        ]);

        echo "Fee marked as paid successfully!\n";
        echo "Amount paid: Rs. "
            . number_format((float) $fee['amount'], 2) . "\n";

    } catch (PDOException $e) {
        echo "Database operation failed: "
            . $e->getMessage() . "\n";
    }
}



function markBookAsLost(PDO $pdo): void
{
    echo "\n--- Mark Book as Lost ---\n";

    $borrowingId = (int) getInput("Enter Borrowing ID: ");

    if ($borrowingId <= 0) {
        echo "Please enter a valid Borrowing ID.\n";
        return;
    }

    try {
        $pdo->beginTransaction();

        // Find the borrowing record and book price
        $sql = "SELECT
                    b.borrowing_id,
                    b.user_id,
                    b.item_id,
                    b.return_date,
                    b.borrowing_status,
                    u.user_name,
                    bk.title,
                    bk.price
                FROM borrowing AS b
                JOIN library_user AS u
                    ON b.user_id = u.user_id
                JOIN book AS bk
                    ON b.item_id = bk.item_id
                WHERE b.borrowing_id = :borrowing_id
                FOR UPDATE";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'borrowing_id' => $borrowingId
        ]);

        $borrowing = $stmt->fetch();

        if (!$borrowing) {
            $pdo->rollBack();
            echo "Borrowing record not found.\n";
            return;
        }

        // Ensure the book is currently borrowed
        if (
            $borrowing['borrowing_status'] !== 'BORROWED'
            || $borrowing['return_date'] !== null
        ) {
            $pdo->rollBack();
            echo "This book is not currently borrowed. "
                . "It may already be returned or marked as lost.\n";
            return;
        }

        // Prevent duplicate lost-item fees
        $check = $pdo->prepare(
            "SELECT fee_id
             FROM fee
             WHERE borrowing_id = :borrowing_id
               AND fee_type = 'LOST'
             LIMIT 1"
        );

        $check->execute([
            'borrowing_id' => $borrowingId
        ]);

        if ($check->fetch()) {
            $pdo->rollBack();
            echo "A lost-item fee already exists for this borrowing.\n";
            return;
        }

        // Mark the borrowing record as lost
        $update = $pdo->prepare(
            "UPDATE borrowing
             SET borrowing_status = 'LOST'
             WHERE borrowing_id = :borrowing_id
               AND borrowing_status = 'BORROWED'
               AND return_date IS NULL"
        );

        $update->execute([
            'borrowing_id' => $borrowingId
        ]);

        if ($update->rowCount() !== 1) {
            throw new RuntimeException(
                "Could not update the borrowing status."
            );
        }

        // Create the lost-item fee
        $insert = $pdo->prepare(
            "INSERT INTO fee
                (user_id, borrowing_id, fee_type,
                 amount, fee_date, paid)
             VALUES
                (:user_id, :borrowing_id, 'LOST',
                 :amount, :fee_date, FALSE)"
        );

        $insert->execute([
            'user_id' => $borrowing['user_id'],
            'borrowing_id' => $borrowingId,
            'amount' => $borrowing['price'],
            'fee_date' => date('Y-m-d')
        ]);

        $pdo->commit();

        echo "\nBook marked as lost successfully!\n";
        echo "User   : " . $borrowing['user_name'] . "\n";
        echo "Book   : " . $borrowing['title'] . "\n";
        echo "Status : LOST\n";
        echo "Fee    : Rs. "
            . number_format((float) $borrowing['price'], 2) . "\n";
        echo "Payment: Unpaid\n";

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo "Operation failed: " . $e->getMessage() . "\n";
    }
}
