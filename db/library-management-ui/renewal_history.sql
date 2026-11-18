USE library_db;

CREATE TABLE IF NOT EXISTS borrowing_renewal (
    renewal_id INT AUTO_INCREMENT PRIMARY KEY,
    borrowing_id INT NOT NULL,
    previous_due_date DATE NOT NULL,
    new_due_date DATE NOT NULL,
    renewed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrowing_id) REFERENCES borrowing(borrowing_id),
    INDEX idx_borrowing_renewal_borrowing_id (borrowing_id)
) ENGINE=InnoDB;
