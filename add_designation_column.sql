-- Run this SQL directly in Railway Database Query Console
-- This will add the designation column to the users table

-- For MySQL/MariaDB:
ALTER TABLE users ADD COLUMN designation VARCHAR(255) NULL AFTER department_id;

-- For PostgreSQL (if using PostgreSQL instead):
-- ALTER TABLE users ADD COLUMN designation VARCHAR(255) NULL;

-- Verify the column was added:
-- SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'designation';
